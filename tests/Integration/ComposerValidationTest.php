<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Tests for composer.json validation and package configuration
 */
class ComposerValidationTest extends TestCase
{
    private string $composerPath;
    /** @var array<string, mixed> */
    private array $composerData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->composerPath = dirname(__DIR__, 2) . '/composer.json';

        $this->assertTrue(
            file_exists($this->composerPath),
            'composer.json file must exist'
        );

        $content = file_get_contents($this->composerPath);
        $this->assertIsString($content);

        $decodedData = json_decode($content, true);
        $this->assertIsArray($decodedData);
        $this->composerData = $decodedData;
        $this->assertEquals(
            JSON_ERROR_NONE,
            json_last_error(),
            'composer.json must be valid JSON'
        );
    }

    public function testComposerJsonIsValidJson(): void
    {
        $this->assertNotEmpty($this->composerData);
        $this->assertIsArray($this->composerData);
    }

    public function testRequiredFieldsArePresent(): void
    {
        $requiredFields = ['name', 'description', 'type', 'license', 'require'];

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $this->composerData,
                "Required field '{$field}' must be present in composer.json"
            );
        }
    }

    public function testPackageNameIsValid(): void
    {
        $this->assertArrayHasKey('name', $this->composerData);
        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/',
            $this->composerData['name'],
            'Package name must follow vendor/package format'
        );
    }

    public function testDescriptionIsPresent(): void
    {
        $this->assertArrayHasKey('description', $this->composerData);
        $this->assertNotEmpty($this->composerData['description']);
        $this->assertIsString($this->composerData['description']);
        $this->assertGreaterThan(
            10,
            strlen($this->composerData['description']),
            'Description should be meaningful (more than 10 characters)'
        );
    }

    public function testLicenseIsValid(): void
    {
        $this->assertArrayHasKey('license', $this->composerData);
        $validLicenses = ['MIT', 'BSD-3-Clause', 'BSD-2-Clause', 'GPL-3.0', 'Apache-2.0', 'proprietary'];
        $this->assertContains(
            $this->composerData['license'],
            $validLicenses,
            'License must be a valid SPDX identifier'
        );
    }

    public function testAutoloadingConfiguration(): void
    {
        $this->assertArrayHasKey('autoload', $this->composerData);
        $this->assertArrayHasKey('psr-4', $this->composerData['autoload']);
        $this->assertArrayHasKey('SortedLinkedList\\', $this->composerData['autoload']['psr-4']);
        $this->assertEquals('src/', $this->composerData['autoload']['psr-4']['SortedLinkedList\\']);
    }

    public function testAutoloadDevConfiguration(): void
    {
        $this->assertArrayHasKey('autoload-dev', $this->composerData);
        $this->assertArrayHasKey('psr-4', $this->composerData['autoload-dev']);
        $this->assertArrayHasKey('SortedLinkedList\\Tests\\', $this->composerData['autoload-dev']['psr-4']);
        $this->assertEquals('tests/', $this->composerData['autoload-dev']['psr-4']['SortedLinkedList\\Tests\\']);
    }

    public function testPhpVersionRequirement(): void
    {
        $this->assertArrayHasKey('require', $this->composerData);
        $this->assertArrayHasKey('php', $this->composerData['require']);
        $this->assertMatchesRegularExpression(
            '/^\^8\.[1-9]|\^8\.\d{2}|>=8\.1/',
            $this->composerData['require']['php'],
            'PHP version must be 8.1 or higher'
        );
    }

    public function testKeywordsForDiscoverability(): void
    {
        $this->assertArrayHasKey('keywords', $this->composerData);
        $this->assertIsArray($this->composerData['keywords']);
        $this->assertNotEmpty($this->composerData['keywords']);
        $this->assertGreaterThanOrEqual(
            3,
            count($this->composerData['keywords']),
            'Should have at least 3 keywords for better discoverability'
        );
    }

    public function testAuthorsInformation(): void
    {
        $this->assertArrayHasKey('authors', $this->composerData);
        $this->assertIsArray($this->composerData['authors']);
        $this->assertNotEmpty($this->composerData['authors']);

        $authors = $this->composerData['authors'];
        $this->assertIsArray($authors);
        foreach ($authors as $author) {
            $this->assertIsArray($author);
            $this->assertArrayHasKey('name', $author);
            $this->assertNotEmpty($author['name']);
        }
    }

    public function testHomepageIsValid(): void
    {
        if (isset($this->composerData['homepage'])) {
            $this->assertMatchesRegularExpression(
                '/^https?:\/\/.+/',
                $this->composerData['homepage'],
                'Homepage must be a valid URL'
            );
        }
    }

    public function testSupportLinksArePresent(): void
    {
        if (isset($this->composerData['support'])) {
            $this->assertIsArray($this->composerData['support']);

            $support = $this->composerData['support'];
            $this->assertIsArray($support);

            if (isset($support['issues'])) {
                $issuesUrl = $support['issues'];
                $this->assertIsString($issuesUrl);
                $this->assertMatchesRegularExpression(
                    '/^https?:\/\/.+/',
                    $issuesUrl,
                    'Issues URL must be valid'
                );
            }

            if (isset($support['source'])) {
                $sourceUrl = $support['source'];
                $this->assertIsString($sourceUrl);
                $this->assertMatchesRegularExpression(
                    '/^https?:\/\/.+/',
                    $sourceUrl,
                    'Source URL must be valid'
                );
            }
        }
    }

    public function testComposerValidateCommand(): void
    {
        $output = [];
        $returnCode = 0;
        exec('composer validate --strict --no-check-publish 2>&1', $output, $returnCode);

        $this->assertEquals(
            0,
            $returnCode,
            'composer validate must pass. Output: ' . implode("\n", $output)
        );
    }

    public function testMinimumStabilityConfiguration(): void
    {
        if (isset($this->composerData['minimum-stability'])) {
            $validStabilities = ['dev', 'alpha', 'beta', 'RC', 'stable'];
            $this->assertContains(
                $this->composerData['minimum-stability'],
                $validStabilities,
                'Minimum stability must be valid'
            );
        }
    }

    public function testPreferStableConfiguration(): void
    {
        if (isset($this->composerData['prefer-stable'])) {
            $this->assertIsBool($this->composerData['prefer-stable']);
        }
    }

    public function testScriptsConfiguration(): void
    {
        if (isset($this->composerData['scripts'])) {
            $this->assertIsArray($this->composerData['scripts']);

            // Common scripts that should be present
            $recommendedScripts = ['test', 'analyse'];
            foreach ($recommendedScripts as $script) {
                $this->assertArrayHasKey(
                    $script,
                    $this->composerData['scripts'],
                    "Recommended script '{$script}' should be defined"
                );
            }
        }
    }
}