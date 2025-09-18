<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Documentation;

use PHPUnit\Framework\TestCase;

/**
 * Tests for documentation generation workflow.
 *
 * @group documentation
 */
class DocumentationGenerationTest extends TestCase
{
    /**
     * Test that phpdoc configuration file exists.
     *
     * @test
     */
    public function testPhpDocConfigurationExists(): void
    {
        $configFiles = [
            dirname(__DIR__, 2) . '/phpdoc.dist.xml',
            dirname(__DIR__, 2) . '/phpdoc.xml',
        ];

        $configExists = false;
        foreach ($configFiles as $configFile) {
            if (file_exists($configFile)) {
                $configExists = true;
                break;
            }
        }

        $this->assertTrue(
            $configExists,
            'phpDocumentor configuration file (phpdoc.dist.xml or phpdoc.xml) should exist'
        );
    }

    /**
     * Test that composer.json contains documentation generation script.
     *
     * @test
     */
    public function testComposerHasDocumentationScript(): void
    {
        $composerFile = dirname(__DIR__, 2) . '/composer.json';
        $this->assertFileExists($composerFile);

        $composerContent = json_decode(file_get_contents($composerFile), true);
        $this->assertIsArray($composerContent);
        $this->assertArrayHasKey('scripts', $composerContent);
        $this->assertArrayHasKey('docs', $composerContent['scripts'], 'Composer should have a "docs" script');
    }

    /**
     * Test that phpDocumentor is installed as dev dependency.
     *
     * @test
     */
    public function testPhpDocumentorIsInstalled(): void
    {
        $composerFile = dirname(__DIR__, 2) . '/composer.json';
        $this->assertFileExists($composerFile);

        $composerContent = json_decode(file_get_contents($composerFile), true);
        $this->assertArrayHasKey('require-dev', $composerContent);
        $this->assertArrayHasKey(
            'phpdocumentor/phpdocumentor',
            $composerContent['require-dev'],
            'phpDocumentor should be installed as a dev dependency'
        );
    }

    /**
     * Test that phpdoc configuration is valid XML.
     *
     * @test
     */
    public function testPhpDocConfigurationIsValidXml(): void
    {
        $configFiles = [
            dirname(__DIR__, 2) . '/phpdoc.dist.xml',
            dirname(__DIR__, 2) . '/phpdoc.xml',
        ];

        $configFile = null;
        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                $configFile = $file;
                break;
            }
        }

        if ($configFile === null) {
            $this->markTestSkipped('No phpDocumentor configuration file found');
        }

        $xmlContent = file_get_contents($configFile);
        $xml = simplexml_load_string($xmlContent);
        $this->assertNotFalse($xml, 'phpDocumentor configuration should be valid XML');

        // Register the namespace for XPath queries
        $xml->registerXPathNamespace('pd', 'https://www.phpdoc.org');

        // Check for required elements
        $paths = $xml->xpath('//pd:paths');
        $this->assertNotEmpty($paths, 'Configuration should define paths');

        $version = $xml->xpath('//pd:version');
        $this->assertNotEmpty($version, 'Configuration should define version');
    }

    /**
     * Test that documentation output directory is configured.
     *
     * @test
     */
    public function testDocumentationOutputDirectoryConfigured(): void
    {
        $configFiles = [
            dirname(__DIR__, 2) . '/phpdoc.dist.xml',
            dirname(__DIR__, 2) . '/phpdoc.xml',
        ];

        $configFile = null;
        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                $configFile = $file;
                break;
            }
        }

        if ($configFile === null) {
            $this->markTestSkipped('No phpDocumentor configuration file found');
        }

        $xmlContent = file_get_contents($configFile);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $this->fail('Failed to parse XML configuration');
        }

        // Register the namespace for XPath queries
        $xml->registerXPathNamespace('pd', 'https://www.phpdoc.org');
        $outputPaths = $xml->xpath('//pd:paths/pd:output');

        $this->assertNotEmpty($outputPaths, 'Output path should be configured');
        $this->assertStringContainsString('docs', (string)$outputPaths[0], 'Output should be in docs directory');
    }

    /**
     * Test that all public classes have PHPDoc blocks.
     *
     * @test
     * @dataProvider publicClassProvider
     */
    public function testPublicClassesHaveDocumentation(string $className): void
    {
        $reflection = new \ReflectionClass($className);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse(
            $docComment,
            "Class {$className} should have a PHPDoc block"
        );

        $this->assertStringContainsString(
            '@',
            $docComment,
            "Class {$className} PHPDoc should contain annotations"
        );
    }

    /**
     * Test that documentation can be generated without errors.
     *
     * @test
     * @group integration
     */
    public function testDocumentationGenerationSucceeds(): void
    {
        // Check if phpDocumentor is available
        $phpDocBinary = dirname(__DIR__, 2) . '/vendor/bin/phpdoc';
        if (!file_exists($phpDocBinary)) {
            $this->markTestSkipped('phpDocumentor is not installed');
        }

        // Check if configuration exists
        $configFile = null;
        $configFiles = [
            dirname(__DIR__, 2) . '/phpdoc.dist.xml',
            dirname(__DIR__, 2) . '/phpdoc.xml',
        ];

        foreach ($configFiles as $file) {
            if (file_exists($file)) {
                $configFile = $file;
                break;
            }
        }

        if ($configFile === null) {
            $this->markTestSkipped('No phpDocumentor configuration file found');
        }

        // Try to run documentation generation with dry-run or validate
        $output = [];
        $returnCode = 0;
        exec("php {$phpDocBinary} --config={$configFile} --validate 2>&1", $output, $returnCode);

        $this->assertEquals(
            0,
            $returnCode,
            'Documentation generation validation should succeed. Output: ' . implode("\n", $output)
        );
    }

    /**
     * Provides list of public classes that should be documented.
     *
     * @return array<array<string>>
     */
    public function publicClassProvider(): array
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
