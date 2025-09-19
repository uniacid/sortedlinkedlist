<?php

declare(strict_types=1);

namespace SortedLinkedList\Benchmarks;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\OutputMode;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\Comparator\NumericComparator;

/**
 * @BeforeMethods("setUp")
 * @OutputTimeUnit("microseconds", precision=3)
 * @OutputMode("throughput")
 * @Groups({"memory", "comparison"})
 */
class MemoryUsageBench extends AbstractBenchmark
{
    private array $integerData = [];
    private array $stringData = [];
    private array $floatData = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];

        // Generate test data
        $this->integerData = [];
        $this->stringData = [];
        $this->floatData = [];

        for ($i = 0; $i < $size; $i++) {
            $this->integerData[] = $i;
            $this->stringData[] = sprintf('string_%010d', $i);
            $this->floatData[] = $i * 1.1;
        }
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function provideDataSizes(): array
    {
        return $this->provideMemoryDataSizes();
    }

    /**
     * Benchmark memory usage for integer sorted linked list
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchIntegerListMemory(array $params): void
    {
        $list = new IntegerSortedLinkedList();
        foreach ($this->integerData as $value) {
            $list->add($value);
        }
        // Force memory allocation
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark memory usage for native integer array
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchNativeIntegerArrayMemory(array $params): void
    {
        $array = [];
        foreach ($this->integerData as $value) {
            $array[] = $value;
        }
        sort($array);
        // Force memory allocation
        count($array);
        $copy = array_values($array);
    }

    /**
     * Benchmark memory usage for string sorted linked list
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchStringListMemory(array $params): void
    {
        $list = new StringSortedLinkedList();
        foreach ($this->stringData as $value) {
            $list->add($value);
        }
        // Force memory allocation
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark memory usage for native string array
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchNativeStringArrayMemory(array $params): void
    {
        $array = [];
        foreach ($this->stringData as $value) {
            $array[] = $value;
        }
        sort($array);
        // Force memory allocation
        count($array);
        $copy = array_values($array);
    }

    /**
     * Benchmark memory usage for float sorted linked list
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchFloatListMemory(array $params): void
    {
        $list = new FloatSortedLinkedList();
        foreach ($this->floatData as $value) {
            $list->add($value);
        }
        // Force memory allocation
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark memory usage for immutable sorted linked list
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchImmutableListMemory(array $params): void
    {
        $list = new ImmutableSortedLinkedList(new NumericComparator());
        foreach ($this->integerData as $value) {
            $list = $list->add($value);
        }
        // Force memory allocation
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark memory overhead of multiple immutable versions
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchImmutableVersionsMemory(array $params): void
    {
        $versions = [];
        $list = new ImmutableSortedLinkedList(new NumericComparator());

        // Build initial list
        foreach ($this->integerData as $value) {
            $list = $list->add($value);
        }

        // Create 10 versions with small modifications
        for ($i = 0; $i < 10; $i++) {
            $list = $list->add(100000 + $i);
            $versions[] = $list;
        }

        // Force memory allocation for all versions
        foreach ($versions as $version) {
            $version->size();
        }
    }

    /**
     * Benchmark memory usage after bulk operations
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchBulkOperationsMemory(array $params): void
    {
        $list = new IntegerSortedLinkedList();
        $list->addAll($this->integerData);

        // Perform bulk operations
        $filtered = $list->filter(function ($value) {
            return $value % 2 === 0;
        });
        $mapped = $filtered->map(function ($value) {
            return $value * 2;
        });

        // Force memory allocation
        $list->size();
        $filtered->size();
        $mapped->size();
        $mapped->toArray();
    }

    /**
     * Benchmark memory usage with iterator
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchIteratorMemory(array $params): void
    {
        $list = new IntegerSortedLinkedList();
        foreach ($this->integerData as $value) {
            $list->add($value);
        }

        // Iterate through entire list
        $sum = 0;
        foreach ($list as $value) {
            $sum += $value;
        }

        // Check iterator state memory
        $list->rewind();
        $list->end();
        $list->current();
    }

    /**
     * Benchmark memory usage with performance stats tracking
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchStatsTrackingMemory(array $params): void
    {
        $list = new IntegerSortedLinkedList();

        // Add elements with stats tracking
        foreach ($this->integerData as $value) {
            $list->add($value);
        }

        // Perform operations that update stats
        for ($i = 0; $i < 100; $i++) {
            $list->contains($i);
            $list->binarySearch($i);
        }

        // Get stats (should track counters)
        $stats = $list->getStats();
    }

    /**
     * Benchmark peak memory during large batch insertion
     * @ParamProviders("provideDataSizes")
     * @Revs(5)
     * @Iterations(3)
     */
    public function benchPeakMemoryDuringInsertion(array $params): void
    {
        $list = new IntegerSortedLinkedList();
        $batchSize = 100;

        for ($i = 0; $i < $params['size']; $i += $batchSize) {
            $batch = array_slice($this->integerData, $i, $batchSize);
            $list->addAll($batch);
        }

        // Final memory check
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark memory comparison: linked list vs array for sparse data
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchSparseDataMemory(array $params): void
    {
        $list = new IntegerSortedLinkedList();

        // Add sparse data (large gaps between values)
        for ($i = 0; $i < $params['size']; $i++) {
            $list->add($i * 1000);
        }

        // Force memory allocation
        $list->size();
        $list->toArray();
    }

    /**
     * Benchmark native sparse array memory
     * @ParamProviders("provideDataSizes")
     * @Revs(10)
     * @Iterations(3)
     */
    public function benchNativeSparseArrayMemory(array $params): void
    {
        $array = [];

        // Add sparse data
        for ($i = 0; $i < $params['size']; $i++) {
            $array[] = $i * 1000;
        }

        sort($array);
        // Force memory allocation
        count($array);
        $copy = array_values($array);
    }
}
