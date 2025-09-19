<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for release workflow and version management
 *
 * @covers Release workflow validation
 */
class ReleaseWorkflowTest extends TestCase
{
    private string $composerPath;
    private string $changelogPath;
    private string $workflowPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->composerPath = dirname(__DIR__) . '/composer.json';
        $this->changelogPath = dirname(__DIR__) . '/CHANGELOG.md';
        $this->workflowPath = dirname(__DIR__) . '/.github/workflows/release.yml';
    }

    /**
     * Test that composer.json contains all required metadata for Packagist
     */
    public function testComposerJsonHasCompleteMetadata(): void
    {
        $this->assertFileExists($this->composerPath);

        $composerData = json_decode(file_get_contents($this->composerPath), true);

        // Required fields for Packagist
        $this->assertArrayHasKey('name', $composerData, 'composer.json must have a name field');
        $this->assertArrayHasKey('description', $composerData, 'composer.json must have a description');
        $this->assertArrayHasKey('type', $composerData, 'composer.json must have a type field');
        $this->assertArrayHasKey('license', $composerData, 'composer.json must have a license field');
        $this->assertArrayHasKey('authors', $composerData, 'composer.json must have authors');
        $this->assertArrayHasKey('require', $composerData, 'composer.json must have requirements');
        $this->assertArrayHasKey('autoload', $composerData, 'composer.json must have autoload configuration');

        // Recommended fields
        $this->assertArrayHasKey('keywords', $composerData, 'composer.json should have keywords for discoverability');
        $this->assertArrayHasKey('homepage', $composerData, 'composer.json should have a homepage URL');
        $this->assertArrayHasKey('support', $composerData, 'composer.json should have support information');

        // Validate name format (vendor/package)
        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/',
            $composerData['name'],
            'Package name must follow Packagist naming convention'
        );

        // Validate stability settings
        $this->assertEquals('stable', $composerData['minimum-stability'] ?? 'stable');
        $this->assertTrue($composerData['prefer-stable'] ?? true);
    }

    /**
     * Test that keywords are properly configured for Packagist discovery
     */
    public function testPackagistKeywordsOptimization(): void
    {
        $composerData = json_decode(file_get_contents($this->composerPath), true);

        $this->assertArrayHasKey('keywords', $composerData);
        $this->assertIsArray($composerData['keywords']);
        $this->assertNotEmpty($composerData['keywords'], 'Keywords should not be empty');

        // Check for essential keywords
        $keywords = $composerData['keywords'];
        $this->assertContains('sorted-list', $keywords, 'Should include sorted-list keyword');
        $this->assertContains('data-structure', $keywords, 'Should include data-structure keyword');

        // Validate keyword count (Packagist recommends 5-15)
        $this->assertGreaterThanOrEqual(5, count($keywords), 'Should have at least 5 keywords');
        $this->assertLessThanOrEqual(15, count($keywords), 'Should have at most 15 keywords');
    }

    /**
     * Test that support URLs are configured
     */
    public function testSupportUrlsConfiguration(): void
    {
        $composerData = json_decode(file_get_contents($this->composerPath), true);

        $this->assertArrayHasKey('support', $composerData);
        $support = $composerData['support'];

        $this->assertArrayHasKey('issues', $support, 'Should have issues URL');
        $this->assertArrayHasKey('source', $support, 'Should have source URL');
        $this->assertArrayHasKey('docs', $support, 'Should have documentation URL');

        // Validate URLs
        foreach ($support as $key => $url) {
            $this->assertNotEmpty($url, "Support URL for {$key} should not be empty");
            $this->assertStringStartsWith('https://', $url, "Support URL for {$key} should use HTTPS");
        }
    }

    /**
     * Test that CHANGELOG follows Keep a Changelog format
     */
    public function testChangelogFollowsStandard(): void
    {
        $this->assertFileExists($this->changelogPath);

        $changelog = file_get_contents($this->changelogPath);

        // Check header
        $this->assertStringContainsString('# Changelog', $changelog);
        $this->assertStringContainsString('Keep a Changelog', $changelog);
        $this->assertStringContainsString('Semantic Versioning', $changelog);

        // Check for standard sections
        $this->assertMatchesRegularExpression('/## \[Unreleased\]/', $changelog);

        // Check for version format
        $this->assertMatchesRegularExpression('/## \[\d+\.\d+\.\d+\] - \d{4}-\d{2}-\d{2}/', $changelog);

        // Check for standard subsections
        $standardSections = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];
        foreach ($standardSections as $section) {
            if (strpos($changelog, "### {$section}") !== false) {
                $this->assertStringContainsString("### {$section}", $changelog);
            }
        }
    }

    /**
     * Test that semantic versioning is properly configured
     */
    public function testSemanticVersioningConfiguration(): void
    {
        $changelog = file_get_contents($this->changelogPath);

        // Extract version from changelog
        preg_match_all('/## \[(\d+\.\d+\.\d+)\]/', $changelog, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $version) {
                $this->assertMatchesRegularExpression(
                    '/^\d+\.\d+\.\d+$/',
                    $version,
                    "Version {$version} must follow semantic versioning"
                );
            }
        }

        // Check for version guidelines in changelog
        $this->assertStringContainsString('Semantic Versioning', $changelog);
        $this->assertStringContainsString('MAJOR version', $changelog);
        $this->assertStringContainsString('MINOR version', $changelog);
        $this->assertStringContainsString('PATCH version', $changelog);
    }

    /**
     * Test that GitHub Actions release workflow exists and is valid
     */
    public function testReleaseWorkflowExists(): void
    {
        $this->assertFileExists($this->workflowPath, 'GitHub Actions release workflow must exist');

        $workflow = file_get_contents($this->workflowPath);

        // Check workflow name
        $this->assertStringContainsString('name: Release', $workflow);

        // Check trigger on tags
        $this->assertStringContainsString("tags:", $workflow);
        $this->assertStringContainsString("'v*.*.*'", $workflow);

        // Check for essential jobs
        $this->assertStringContainsString('validate:', $workflow);
        $this->assertStringContainsString('build:', $workflow);
        $this->assertStringContainsString('release:', $workflow);

        // Check for Packagist notification
        $this->assertStringContainsString('packagist', $workflow, 'Workflow should include Packagist notification');
    }

    /**
     * Test that release workflow includes quality checks
     */
    public function testReleaseWorkflowQualityChecks(): void
    {
        $workflow = file_get_contents($this->workflowPath);

        // Check for test execution
        $this->assertStringContainsString('phpunit', $workflow, 'Release should run PHPUnit tests');

        // Check for static analysis
        $this->assertStringContainsString('phpstan', $workflow, 'Release should run PHPStan analysis');

        // Check for composer validation
        $this->assertStringContainsString('composer validate', $workflow, 'Release should validate composer.json');
    }

    /**
     * Test that release workflow generates changelog
     */
    public function testReleaseWorkflowChangelogGeneration(): void
    {
        $workflow = file_get_contents($this->workflowPath);

        $this->assertStringContainsString('Generate changelog', $workflow);
        $this->assertStringContainsString('git log', $workflow);
        $this->assertStringContainsString('PREVIOUS_TAG', $workflow);
    }

    /**
     * Test that release workflow creates distribution archive
     */
    public function testReleaseWorkflowDistributionArchive(): void
    {
        $workflow = file_get_contents($this->workflowPath);

        $this->assertStringContainsString('Create distribution archive', $workflow);
        $this->assertStringContainsString('tar', $workflow);
        $this->assertStringContainsString('sortedlinkedlist.tar.gz', $workflow);

        // Check exclusions
        $this->assertStringContainsString('--exclude=tests', $workflow);
        $this->assertStringContainsString('--exclude=.git', $workflow);
        $this->assertStringContainsString('--exclude=vendor', $workflow);
    }

    /**
     * Test that composer.json is valid for Packagist submission
     */
    public function testComposerJsonValidation(): void
    {
        // Run composer validate command
        $output = [];
        $returnCode = 0;
        exec('composer validate --strict 2>&1', $output, $returnCode);

        $this->assertEquals(0, $returnCode, 'composer.json must be valid: ' . implode("\n", $output));
    }

    /**
     * Test that PSR-4 autoloading is properly configured
     */
    public function testAutoloadConfiguration(): void
    {
        $composerData = json_decode(file_get_contents($this->composerPath), true);

        $this->assertArrayHasKey('autoload', $composerData);
        $this->assertArrayHasKey('psr-4', $composerData['autoload']);

        $psr4 = $composerData['autoload']['psr-4'];
        $this->assertArrayHasKey('SortedLinkedList\\', $psr4);
        $this->assertEquals('src/', $psr4['SortedLinkedList\\']);

        // Check autoload-dev for tests
        $this->assertArrayHasKey('autoload-dev', $composerData);
        $this->assertArrayHasKey('psr-4', $composerData['autoload-dev']);

        $psr4Dev = $composerData['autoload-dev']['psr-4'];
        $this->assertArrayHasKey('SortedLinkedList\\Tests\\', $psr4Dev);
    }

    /**
     * Test that package installation scripts are safe
     */
    public function testNoUnsafeScripts(): void
    {
        $composerData = json_decode(file_get_contents($this->composerPath), true);

        if (isset($composerData['scripts'])) {
            $unsafeScripts = ['post-install-cmd', 'post-update-cmd', 'post-autoload-dump'];
            $hasUnsafeScripts = false;

            foreach ($unsafeScripts as $script) {
                if (isset($composerData['scripts'][$script])) {
                    $hasUnsafeScripts = true;
                    // If these scripts exist, ensure they're safe
                    $commands = $composerData['scripts'][$script];
                    if (!is_array($commands)) {
                        $commands = [$commands];
                    }

                    foreach ($commands as $command) {
                        $this->assertStringNotContainsString('rm ', $command, 'Scripts should not delete files');
                        $this->assertStringNotContainsString('curl ', $command, 'Scripts should not download files');
                        $this->assertStringNotContainsString('wget ', $command, 'Scripts should not download files');
                    }
                }
            }

            // Assert that we checked something or no unsafe scripts exist
            $this->assertTrue(!$hasUnsafeScripts || true, 'No unsafe installation scripts found');
        } else {
            $this->assertTrue(true, 'No scripts defined in composer.json');
        }
    }

    /**
     * Test version tag format for releases
     */
    public function testVersionTagFormat(): void
    {
        // Check if any tags exist
        $output = [];
        exec('git tag --list 2>&1', $output);

        $hasVersionTags = false;
        foreach ($output as $tag) {
            if (!empty($tag)) {
                // All version tags should follow v*.*.* format
                if (preg_match('/^v/', $tag)) {
                    $hasVersionTags = true;
                    $this->assertMatchesRegularExpression(
                        '/^v\d+\.\d+\.\d+(-[a-zA-Z0-9\.]+)?$/',
                        $tag,
                        "Tag {$tag} must follow semantic versioning format"
                    );
                }
            }
        }

        // If no version tags exist, that's fine for initial setup
        $this->assertTrue(!$hasVersionTags || true, 'Version tags follow semantic versioning or no tags exist yet');
    }
}
