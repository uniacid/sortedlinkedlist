<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\CI;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class WorkflowValidationTest extends TestCase
{
    private const WORKFLOW_DIR = __DIR__ . '/../../.github/workflows';
    private const REQUIRED_WORKFLOWS = [
        'ci.yml',
        'deploy-docs.yml',
        'benchmarks.yml'
    ];

    private const REQUIRED_JOBS = [
        'ci.yml' => ['test', 'coverage', 'documentation'],
        'deploy-docs.yml' => ['deploy'],
        'benchmarks.yml' => ['benchmark']
    ];

    private const REQUIRED_PHP_VERSIONS = ['8.1', '8.2', '8.3'];

    public function testWorkflowFilesExist(): void
    {
        foreach (self::REQUIRED_WORKFLOWS as $workflow) {
            $path = self::WORKFLOW_DIR . '/' . $workflow;
            $this->assertFileExists($path, "Workflow file {$workflow} does not exist");
        }
    }

    public function testWorkflowsAreValidYaml(): void
    {
        foreach (self::REQUIRED_WORKFLOWS as $workflow) {
            $path = self::WORKFLOW_DIR . '/' . $workflow;
            $content = file_get_contents($path);

            $this->assertNotFalse($content, "Could not read workflow file {$workflow}");

            try {
                $parsed = Yaml::parse($content);
                $this->assertIsArray($parsed, "Workflow {$workflow} is not valid YAML");
                $this->assertArrayHasKey('name', $parsed, "Workflow {$workflow} missing name");
                $this->assertArrayHasKey('jobs', $parsed, "Workflow {$workflow} missing jobs");
            } catch (\Exception $e) {
                $this->fail("Workflow {$workflow} contains invalid YAML: " . $e->getMessage());
            }
        }
    }

    public function testCiWorkflowHasRequiredJobs(): void
    {
        $path = self::WORKFLOW_DIR . '/ci.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertIsArray($workflow);
        $this->assertArrayHasKey('jobs', $workflow);

        foreach (self::REQUIRED_JOBS['ci.yml'] as $job) {
            $this->assertArrayHasKey(
                $job,
                $workflow['jobs'],
                "CI workflow missing required job: {$job}"
            );
        }
    }

    public function testCiWorkflowTestsMultiplePhpVersions(): void
    {
        $path = self::WORKFLOW_DIR . '/ci.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertIsArray($workflow);
        $this->assertArrayHasKey('jobs', $workflow);
        $this->assertArrayHasKey('test', $workflow['jobs']);
        $this->assertArrayHasKey('strategy', $workflow['jobs']['test']);
        $this->assertArrayHasKey('matrix', $workflow['jobs']['test']['strategy']);
        $this->assertArrayHasKey('php-version', $workflow['jobs']['test']['strategy']['matrix']);

        $phpVersions = $workflow['jobs']['test']['strategy']['matrix']['php-version'];

        foreach (self::REQUIRED_PHP_VERSIONS as $version) {
            $this->assertContains(
                $version,
                $phpVersions,
                "CI workflow does not test PHP {$version}"
            );
        }
    }

    public function testCiWorkflowRunsOnCorrectEvents(): void
    {
        $path = self::WORKFLOW_DIR . '/ci.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertArrayHasKey('on', $workflow, 'CI workflow missing triggers');

        $triggers = $workflow['on'];
        $this->assertArrayHasKey('push', $triggers, 'CI workflow should run on push');
        $this->assertArrayHasKey('pull_request', $triggers, 'CI workflow should run on pull_request');
    }

    public function testCiWorkflowHasCoverageReporting(): void
    {
        $path = self::WORKFLOW_DIR . '/ci.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertIsArray($workflow);
        $this->assertArrayHasKey('jobs', $workflow);
        $this->assertArrayHasKey('coverage', $workflow['jobs']);

        $coverageJob = $workflow['jobs']['coverage'];
        $this->assertArrayHasKey('steps', $coverageJob);
        $this->assertIsArray($coverageJob['steps']);

        $hasCodecov = false;

        foreach ($coverageJob['steps'] as $step) {
            if (isset($step['uses']) && str_contains($step['uses'], 'codecov')) {
                $hasCodecov = true;
                break;
            }
        }

        $this->assertTrue($hasCodecov, 'CI workflow should upload coverage to Codecov');
    }

    public function testCiWorkflowRunsPhpstan(): void
    {
        $path = self::WORKFLOW_DIR . '/ci.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertIsArray($workflow);
        $this->assertArrayHasKey('jobs', $workflow);

        $hasPhpstan = false;

        foreach ($workflow['jobs'] as $job) {
            if (!is_array($job) || !isset($job['steps']) || !is_array($job['steps'])) {
                continue;
            }
            foreach ($job['steps'] as $step) {
                if (isset($step['run']) && str_contains((string) $step['run'], 'phpstan')) {
                    $hasPhpstan = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasPhpstan, 'CI workflow should run PHPStan analysis');
    }

    public function testDeployDocsWorkflowConfiguration(): void
    {
        $path = self::WORKFLOW_DIR . '/deploy-docs.yml';
        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertArrayHasKey('on', $workflow);
        $triggers = $workflow['on'];

        $this->assertArrayHasKey('push', $triggers, 'Deploy docs should trigger on push');
        $this->assertArrayHasKey('workflow_dispatch', $triggers, 'Deploy docs should allow manual trigger');
    }

    public function testWorkflowsUseLatestActions(): void
    {
        foreach (self::REQUIRED_WORKFLOWS as $workflowFile) {
            $path = self::WORKFLOW_DIR . '/' . $workflowFile;
            $content = file_get_contents($path);
            $workflow = Yaml::parse($content);

            $this->assertIsArray($workflow);
            $this->assertArrayHasKey('jobs', $workflow);

            foreach ($workflow['jobs'] as $jobName => $job) {
                if (!is_array($job) || !isset($job['steps']) || !is_array($job['steps'])) {
                    continue;
                }

                foreach ($job['steps'] as $stepIndex => $step) {
                    if (!isset($step['uses'])) {
                        continue;
                    }

                    $jobNameStr = (string) $jobName;

                    if (str_starts_with($step['uses'], 'actions/checkout')) {
                        $this->assertStringContainsString(
                            '@v4',
                            $step['uses'],
                            "Workflow {$workflowFile} job {$jobNameStr} should use checkout@v4"
                        );
                    }

                    if (str_starts_with($step['uses'], 'actions/cache')) {
                        $this->assertStringContainsString(
                            '@v4',
                            $step['uses'],
                            "Workflow {$workflowFile} job {$jobNameStr} should use cache@v4"
                        );
                    }

                    if (str_starts_with($step['uses'], 'shivammathur/setup-php')) {
                        $this->assertStringContainsString(
                            '@v2',
                            $step['uses'],
                            "Workflow {$workflowFile} job {$jobNameStr} should use setup-php@v2"
                        );
                    }
                }
            }
        }
    }

    public function testBenchmarkWorkflowConfiguration(): void
    {
        $path = self::WORKFLOW_DIR . '/benchmarks.yml';

        if (!file_exists($path)) {
            $this->markTestSkipped('Benchmarks workflow not yet implemented');
        }

        $content = file_get_contents($path);
        $workflow = Yaml::parse($content);

        $this->assertArrayHasKey('jobs', $workflow);
        $this->assertArrayHasKey('benchmark', $workflow['jobs'], 'Benchmark workflow should have benchmark job');
    }
}
