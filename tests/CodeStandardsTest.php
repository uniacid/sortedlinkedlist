<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @covers Code Standards Compliance
 */
class CodeStandardsTest extends TestCase
{
    /**
     * Test that phpcs.xml configuration file exists
     */
    public function testPhpcsConfigurationExists(): void
    {
        $configFile = dirname(__DIR__) . '/phpcs.xml';
        $this->assertFileExists($configFile, 'phpcs.xml configuration file should exist');
    }

    /**
     * Test that phpcs.xml is valid XML
     */
    public function testPhpcsConfigurationIsValid(): void
    {
        $configFile = dirname(__DIR__) . '/phpcs.xml';

        if (!file_exists($configFile)) {
            $this->markTestSkipped('phpcs.xml does not exist yet');
        }

        $xml = @simplexml_load_file($configFile);
        $this->assertNotFalse($xml, 'phpcs.xml should be valid XML');
        $this->assertEquals('ruleset', $xml->getName(), 'Root element should be ruleset');
    }

    /**
     * Test that PHP CodeSniffer is installed
     */
    public function testPhpCodeSnifferIsInstalled(): void
    {
        $phpcsPath = dirname(__DIR__) . '/vendor/bin/phpcs';
        $this->assertFileExists($phpcsPath, 'PHP CodeSniffer should be installed');

        if (file_exists($phpcsPath)) {
            $this->assertTrue(is_executable($phpcsPath), 'phpcs should be executable');
        }
    }

    /**
     * Test that PHPCBF (Code Beautifier and Fixer) is installed
     */
    public function testPhpCodeBeautifierIsInstalled(): void
    {
        $phpcbfPath = dirname(__DIR__) . '/vendor/bin/phpcbf';
        $this->assertFileExists($phpcbfPath, 'PHP Code Beautifier should be installed');

        if (file_exists($phpcbfPath)) {
            $this->assertTrue(is_executable($phpcbfPath), 'phpcbf should be executable');
        }
    }

    /**
     * Test that composer scripts include code standards commands
     */
    public function testComposerScriptsIncludeCodeStandards(): void
    {
        $composerFile = dirname(__DIR__) . '/composer.json';
        $this->assertFileExists($composerFile);

        $composer = json_decode(file_get_contents($composerFile), true);
        $this->assertIsArray($composer);
        $this->assertArrayHasKey('scripts', $composer, 'composer.json should have scripts section');

        $scripts = $composer['scripts'];
        $this->assertArrayHasKey('cs-check', $scripts, 'Should have cs-check script');
        $this->assertArrayHasKey('cs-fix', $scripts, 'Should have cs-fix script');
    }

    /**
     * Test that all PHP files in src/ follow PSR-12
     */
    public function testSourceFilesFollowPsr12(): void
    {
        $phpcsPath = dirname(__DIR__) . '/vendor/bin/phpcs';

        if (!file_exists($phpcsPath)) {
            $this->markTestSkipped('PHP CodeSniffer is not installed');
        }

        $srcPath = dirname(__DIR__) . '/src';
        $configFile = dirname(__DIR__) . '/phpcs.xml';

        if (!file_exists($configFile)) {
            $this->markTestSkipped('phpcs.xml configuration does not exist');
        }

        // Run phpcs on src directory
        $output = [];
        $returnCode = 0;
        exec(
            sprintf(
                '%s --standard=%s --report=json %s 2>&1',
                escapeshellarg($phpcsPath),
                escapeshellarg($configFile),
                escapeshellarg($srcPath)
            ),
            $output,
            $returnCode
        );

        $jsonOutput = implode('', $output);
        $result = json_decode($jsonOutput, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($result) && isset($result['totals'])) {
            $this->assertEquals(0, $result['totals']['errors'], 'Source files should have no PSR-12 errors');
            $this->assertEquals(0, $result['totals']['warnings'], 'Source files should have no PSR-12 warnings');
        } else {
            // Fallback: just check return code
            $this->assertEquals(0, $returnCode, 'phpcs should exit with code 0 (no violations found)');
        }
    }

    /**
     * Test that all PHP files in tests/ follow PSR-12
     */
    public function testTestFilesFollowPsr12(): void
    {
        $phpcsPath = dirname(__DIR__) . '/vendor/bin/phpcs';

        if (!file_exists($phpcsPath)) {
            $this->markTestSkipped('PHP CodeSniffer is not installed');
        }

        $testsPath = dirname(__DIR__) . '/tests';
        $configFile = dirname(__DIR__) . '/phpcs.xml';

        if (!file_exists($configFile)) {
            $this->markTestSkipped('phpcs.xml configuration does not exist');
        }

        // Run phpcs on tests directory
        $output = [];
        $returnCode = 0;
        exec(
            sprintf(
                '%s --standard=%s --report=json %s 2>&1',
                escapeshellarg($phpcsPath),
                escapeshellarg($configFile),
                escapeshellarg($testsPath)
            ),
            $output,
            $returnCode
        );

        $jsonOutput = implode('', $output);
        $result = json_decode($jsonOutput, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($result) && isset($result['totals'])) {
            $this->assertEquals(0, $result['totals']['errors'], 'Test files should have no PSR-12 errors');
            $this->assertEquals(0, $result['totals']['warnings'], 'Test files should have no PSR-12 warnings');
        } else {
            // Fallback: just check return code
            $this->assertEquals(0, $returnCode, 'phpcs should exit with code 0 (no violations found)');
        }
    }

    /**
     * Test that CI workflow includes code standards check
     */
    public function testCiWorkflowIncludesCodeStandardsCheck(): void
    {
        $ciFile = dirname(__DIR__) . '/.github/workflows/ci.yml';
        $this->assertFileExists($ciFile, 'CI workflow file should exist');

        $content = file_get_contents($ciFile);
        $this->assertStringContainsString(
            'PHP CodeSniffer',
            $content,
            'CI workflow should include code standards check'
        );
    }
}
