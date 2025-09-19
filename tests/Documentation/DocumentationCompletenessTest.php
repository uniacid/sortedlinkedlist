<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Documentation;

use PHPUnit\Framework\TestCase;

/**
 * Tests for documentation completeness across the project.
 *
 * @group documentation
 */
class DocumentationCompletenessTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../..';

    /**
     * Test that README.md exists and contains required sections.
     *
     * @test
     */
    public function testReadmeExists(): void
    {
        $readmePath = self::PROJECT_ROOT . '/README.md';
        $this->assertFileExists($readmePath, 'README.md should exist in project root');

        $content = file_get_contents($readmePath);
        $this->assertNotEmpty($content, 'README.md should not be empty');

        // Check for essential sections
        $requiredSections = [
            'Installation',
            'Usage',
            'Features',
            'Requirements',
            'License',
        ];

        foreach ($requiredSections as $section) {
            $this->assertStringContainsStringIgnoringCase(
                $section,
                $content,
                "README.md should contain a '{$section}' section"
            );
        }
    }

    /**
     * Test that README contains badges.
     *
     * @test
     */
    public function testReadmeContainsBadges(): void
    {
        $readmePath = self::PROJECT_ROOT . '/README.md';
        $this->assertFileExists($readmePath);

        $content = file_get_contents($readmePath);

        // Check for common badge patterns
        $this->assertMatchesRegularExpression(
            '/!\[.*\]\(.*\)/',
            $content,
            'README.md should contain at least one badge'
        );

        // Check for specific badge types
        $expectedBadges = [
            'packagist.org',  // Packagist version/downloads
            'github.com.*actions',  // GitHub Actions status
            'coveralls.io|codecov.io|codeclimate.com',  // Coverage badges
        ];

        foreach ($expectedBadges as $badgePattern) {
            $this->assertMatchesRegularExpression(
                "/{$badgePattern}/i",
                $content,
                "README.md should contain a badge for: {$badgePattern}"
            );
        }
    }

    /**
     * Test that README contains installation instructions.
     *
     * @test
     */
    public function testReadmeContainsInstallationInstructions(): void
    {
        $readmePath = self::PROJECT_ROOT . '/README.md';
        $this->assertFileExists($readmePath);

        $content = file_get_contents($readmePath);

        // Check for composer install command
        $this->assertMatchesRegularExpression(
            '/composer\s+require\s+[\w\-\/]+/',
            $content,
            'README.md should contain composer require command'
        );
    }

    /**
     * Test that README contains usage examples.
     *
     * @test
     */
    public function testReadmeContainsUsageExamples(): void
    {
        $readmePath = self::PROJECT_ROOT . '/README.md';
        $this->assertFileExists($readmePath);

        $content = file_get_contents($readmePath);

        // Check for code blocks with PHP
        $this->assertMatchesRegularExpression(
            '/```php.*```/s',
            $content,
            'README.md should contain PHP code examples'
        );

        // Check that examples include main classes
        $mainClasses = [
            'IntegerSortedLinkedList',
            'StringSortedLinkedList',
            'FloatSortedLinkedList',
        ];

        foreach ($mainClasses as $className) {
            $this->assertStringContainsString(
                $className,
                $content,
                "README.md should contain usage example for {$className}"
            );
        }
    }

    /**
     * Test that CONTRIBUTING.md exists and contains guidelines.
     *
     * @test
     */
    public function testContributingGuidelinesExist(): void
    {
        $contributingPath = self::PROJECT_ROOT . '/CONTRIBUTING.md';
        $this->assertFileExists($contributingPath, 'CONTRIBUTING.md should exist');

        $content = file_get_contents($contributingPath);
        $this->assertNotEmpty($content, 'CONTRIBUTING.md should not be empty');

        // Check for essential sections
        $requiredSections = [
            'pull request',
            'issue',
            'code',
            'test',
        ];

        foreach ($requiredSections as $section) {
            $this->assertStringContainsStringIgnoringCase(
                $section,
                $content,
                "CONTRIBUTING.md should mention '{$section}'"
            );
        }
    }

    /**
     * Test that CHANGELOG.md exists and follows format.
     *
     * @test
     */
    public function testChangelogExists(): void
    {
        $changelogPath = self::PROJECT_ROOT . '/CHANGELOG.md';
        $this->assertFileExists($changelogPath, 'CHANGELOG.md should exist');

        $content = file_get_contents($changelogPath);
        $this->assertNotEmpty($content, 'CHANGELOG.md should not be empty');

        // Check for semantic versioning pattern
        $this->assertMatchesRegularExpression(
            '/\[?\d+\.\d+\.\d+\]?/',
            $content,
            'CHANGELOG.md should contain version numbers in semantic versioning format'
        );

        // Check for common changelog sections
        $this->assertStringContainsStringIgnoringCase(
            'changelog',
            $content,
            'CHANGELOG.md should contain "Changelog" heading'
        );
    }

    /**
     * Test that CODE_OF_CONDUCT.md exists.
     *
     * @test
     */
    public function testCodeOfConductExists(): void
    {
        $cocPath = self::PROJECT_ROOT . '/CODE_OF_CONDUCT.md';
        $this->assertFileExists($cocPath, 'CODE_OF_CONDUCT.md should exist');

        $content = file_get_contents($cocPath);
        $this->assertNotEmpty($content, 'CODE_OF_CONDUCT.md should not be empty');

        // Check for essential content
        $requiredTerms = [
            'conduct',
            'behavior',
            'community',
            'respect',
        ];

        foreach ($requiredTerms as $term) {
            $this->assertStringContainsStringIgnoringCase(
                $term,
                $content,
                "CODE_OF_CONDUCT.md should mention '{$term}'"
            );
        }
    }

    /**
     * Test that LICENSE file exists.
     *
     * @test
     */
    public function testLicenseFileExists(): void
    {
        $licensePaths = [
            self::PROJECT_ROOT . '/LICENSE',
            self::PROJECT_ROOT . '/LICENSE.md',
            self::PROJECT_ROOT . '/LICENSE.txt',
        ];

        $licenseExists = false;
        foreach ($licensePaths as $path) {
            if (file_exists($path)) {
                $licenseExists = true;
                $content = file_get_contents($path);
                $this->assertNotEmpty($content, 'LICENSE file should not be empty');
                break;
            }
        }

        $this->assertTrue($licenseExists, 'LICENSE file should exist');
    }

    /**
     * Test that all public methods have PHPDoc blocks.
     *
     * @test
     * @dataProvider publicClassProvider
     */
    public function testPublicMethodsHaveDocumentation(string $className): void
    {
        if (!class_exists($className) && !interface_exists($className)) {
            $this->markTestSkipped("Class {$className} does not exist");
        }

        $reflection = new \ReflectionClass($className);
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($publicMethods as $method) {
            // Skip inherited methods
            if ($method->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            // Skip magic methods except __construct
            if (str_starts_with($method->getName(), '__') && $method->getName() !== '__construct') {
                continue;
            }

            $docComment = $method->getDocComment();
            $this->assertNotFalse(
                $docComment,
                "Method {$className}::{$method->getName()} should have a PHPDoc block"
            );

            // Check for @param tags if method has parameters
            if ($method->getNumberOfParameters() > 0) {
                $this->assertStringContainsString(
                    '@param',
                    $docComment,
                    "Method {$className}::{$method->getName()} should document its parameters"
                );
            }

            // Check for @return tag if method has return type
            if ($method->hasReturnType()) {
                $returnType = $method->getReturnType();
                if ($returnType !== null && $returnType->__toString() !== 'void') {
                    $this->assertStringContainsString(
                        '@return',
                        $docComment,
                        "Method {$className}::{$method->getName()} should document its return type"
                    );
                }
            }
        }
    }

    /**
     * Test that README includes links to documentation.
     *
     * @test
     */
    public function testReadmeIncludesDocumentationLinks(): void
    {
        $readmePath = self::PROJECT_ROOT . '/README.md';
        $this->assertFileExists($readmePath);

        $content = file_get_contents($readmePath);

        // Check for documentation links
        $this->assertMatchesRegularExpression(
            '/\[.*documentation.*\]\(.*\)/i',
            $content,
            'README.md should contain links to documentation'
        );
    }

    /**
     * Test that package has proper Packagist metadata.
     *
     * @test
     */
    public function testComposerJsonHasCompleteMetadata(): void
    {
        $composerPath = self::PROJECT_ROOT . '/composer.json';
        $this->assertFileExists($composerPath);

        $composer = json_decode(file_get_contents($composerPath), true);
        $this->assertIsArray($composer);

        // Check required fields for Packagist
        $requiredFields = [
            'name',
            'description',
            'type',
            'license',
            'authors',
            'require',
            'autoload',
            'keywords',
            'homepage',
        ];

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $composer,
                "composer.json should have '{$field}' field for Packagist"
            );

            if ($field === 'authors') {
                $this->assertNotEmpty($composer[$field], 'Authors field should not be empty');
                $this->assertArrayHasKey('name', $composer[$field][0], 'Author should have a name');
            }

            if ($field === 'keywords') {
                $this->assertNotEmpty($composer[$field], 'Keywords field should not be empty');
                $this->assertGreaterThanOrEqual(3, count($composer[$field]), 'Should have at least 3 keywords');
            }
        }
    }

    /**
     * Test that examples directory exists with sample code.
     *
     * @test
     */
    public function testExamplesDirectoryExists(): void
    {
        $examplesPath = self::PROJECT_ROOT . '/examples';

        if (!is_dir($examplesPath)) {
            // Examples might be in README instead
            $readmePath = self::PROJECT_ROOT . '/README.md';
            $content = file_get_contents($readmePath);

            // Count PHP code blocks as examples
            preg_match_all('/```php.*?```/s', $content, $matches);
            $this->assertGreaterThanOrEqual(
                3,
                count($matches[0]),
                'Should have at least 3 PHP examples in README or examples directory'
            );
        } else {
            $this->assertDirectoryExists($examplesPath, 'Examples directory should exist');

            // Check for example files
            $exampleFiles = glob($examplesPath . '/*.php');
            $this->assertNotEmpty($exampleFiles, 'Examples directory should contain PHP files');
        }
    }

    /**
     * Provides list of public classes that should be documented.
     *
     * @return array<array<string>>
     */
    public static function publicClassProvider(): array
    {
        return [
            ['SortedLinkedList\SortedLinkedList'],
            ['SortedLinkedList\IntegerSortedLinkedList'],
            ['SortedLinkedList\StringSortedLinkedList'],
            ['SortedLinkedList\FloatSortedLinkedList'],
            ['SortedLinkedList\Node'],
            ['SortedLinkedList\ImmutableSortedLinkedList'],
            ['SortedLinkedList\IntegerImmutableSortedLinkedList'],
            ['SortedLinkedList\Comparator\ComparatorInterface'],
            ['SortedLinkedList\Comparator\CallableComparator'],
            ['SortedLinkedList\Comparator\ReverseComparator'],
            ['SortedLinkedList\Comparator\NumericComparator'],
            ['SortedLinkedList\Comparator\StringComparator'],
            ['SortedLinkedList\Comparator\DateComparator'],
            ['SortedLinkedList\Comparator\ComparatorFactory'],
        ];
    }
}
