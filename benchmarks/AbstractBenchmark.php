<?php

declare(strict_types=1);

namespace SortedLinkedList\Benchmarks;

/**
 * Base class for benchmarks with CI-aware data providers.
 */
abstract class AbstractBenchmark
{
    /**
     * Check if running in CI environment.
     *
     * @return bool
     */
    protected function isCI(): bool
    {
        return getenv('CI') === 'true' || getenv('GITHUB_ACTIONS') === 'true';
    }

    /**
     * Get data sizes appropriate for the environment.
     *
     * @return array<string, array{size: int}>
     */
    public function provideDataSizes(): array
    {
        if ($this->isCI()) {
            // Smaller data sets for CI environment
            return [
                'small' => ['size' => 50],
                'medium' => ['size' => 200],
                'large' => ['size' => 500],
                'xlarge' => ['size' => 1000],
            ];
        }

        // Full data sets for local development
        return [
            'small' => ['size' => 100],
            'medium' => ['size' => 500],
            'large' => ['size' => 1000],
            'xlarge' => ['size' => 5000],
        ];
    }

    /**
     * Get memory test data sizes appropriate for the environment.
     *
     * @return array<string, array{size: int}>
     */
    public function provideMemoryDataSizes(): array
    {
        if ($this->isCI()) {
            // Much smaller data sets for CI memory benchmarks
            return [
                'small' => ['size' => 100],
                'medium' => ['size' => 500],
                'large' => ['size' => 2000],
                'xlarge' => ['size' => 5000],
            ];
        }

        // Full data sets for local development
        return [
            'small' => ['size' => 100],
            'medium' => ['size' => 1000],
            'large' => ['size' => 10000],
            'xlarge' => ['size' => 50000],
        ];
    }

    /**
     * Get bulk operation data sizes appropriate for the environment.
     *
     * @return array<string, array{size: int, bulk_size: int}>
     */
    public function provideBulkDataSizes(): array
    {
        if ($this->isCI()) {
            // Smaller bulk operations for CI
            return [
                'small' => ['size' => 50, 'bulk_size' => 25],
                'medium' => ['size' => 200, 'bulk_size' => 100],
                'large' => ['size' => 500, 'bulk_size' => 250],
                'xlarge' => ['size' => 1000, 'bulk_size' => 500],
            ];
        }

        // Full data sets for local development
        return [
            'small' => ['size' => 100, 'bulk_size' => 50],
            'medium' => ['size' => 500, 'bulk_size' => 250],
            'large' => ['size' => 1000, 'bulk_size' => 500],
            'xlarge' => ['size' => 5000, 'bulk_size' => 2500],
        ];
    }

    /**
     * Get search operation data sizes with extended sizes.
     *
     * @return array<string, array{size: int}>
     */
    public function provideSearchDataSizes(): array
    {
        if ($this->isCI()) {
            // Smaller data sets for CI environment
            return [
                'tiny' => ['size' => 10],
                'small' => ['size' => 50],
                'medium' => ['size' => 200],
                'large' => ['size' => 500],
                'xlarge' => ['size' => 1000],
                'xxlarge' => ['size' => 2000],
            ];
        }

        // Full data sets for local development
        return [
            'tiny' => ['size' => 10],
            'small' => ['size' => 100],
            'medium' => ['size' => 500],
            'large' => ['size' => 1000],
            'xlarge' => ['size' => 5000],
            'xxlarge' => ['size' => 10000],
        ];
    }

    /**
     * Get iteration data sizes with extended sizes.
     *
     * @return array<string, array{size: int}>
     */
    public function provideIterationDataSizes(): array
    {
        if ($this->isCI()) {
            // Smaller data sets for CI environment
            return [
                'small' => ['size' => 50],
                'medium' => ['size' => 200],
                'large' => ['size' => 500],
                'xlarge' => ['size' => 1000],
                'xxlarge' => ['size' => 2000],
            ];
        }

        // Full data sets for local development
        return [
            'small' => ['size' => 100],
            'medium' => ['size' => 500],
            'large' => ['size' => 1000],
            'xlarge' => ['size' => 5000],
            'xxlarge' => ['size' => 10000],
        ];
    }
}
