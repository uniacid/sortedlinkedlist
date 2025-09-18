<?php

declare(strict_types=1);

namespace SortedLinkedList\Benchmarks;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Groups;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\OutputTimeUnit;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\Comparator\NumericComparator;
use SortedLinkedList\Comparator\ReverseComparator;

/**
 * @BeforeMethods("setUp")
 * @OutputTimeUnit("microseconds", precision=3)
 * @Groups({"immutable", "comparison"})
 */
class ImmutableOperationsBench
{
    private ImmutableSortedLinkedList $immutableList;
    private IntegerSortedLinkedList $mutableList;
    private array $testData = [];
    private array $bulkData = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];

        // Initialize lists with comparator
        $comparator = new NumericComparator();
        $this->immutableList = new ImmutableSortedLinkedList($comparator);
        $this->mutableList = new IntegerSortedLinkedList();

        // Generate test data
        $this->testData = [];
        for ($i = 0; $i < $size; $i++) {
            $this->testData[] = random_int(1, 10000);
        }

        // Pre-populate lists
        foreach ($this->testData as $value) {
            $this->immutableList = $this->immutableList->add($value);
            $this->mutableList->add($value);
        }

        // Generate bulk data for operations
        $this->bulkData = [];
        for ($i = 0; $i < 10; $i++) {
            $this->bulkData[] = random_int(1, 10000);
        }
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function provideDataSizes(): array
    {
        return [
            'small' => ['size' => 100],
            'medium' => ['size' => 500],
            'large' => ['size' => 1000],
            'xlarge' => ['size' => 5000],
        ];
    }

    /**
     * Benchmark immutable add operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableAdd(array $params): void
    {
        $list = $this->immutableList;
        foreach ($this->bulkData as $value) {
            $list = $list->add($value);
        }
    }

    /**
     * Benchmark mutable add operation for comparison
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchMutableAdd(array $params): void
    {
        $list = clone $this->mutableList;
        foreach ($this->bulkData as $value) {
            $list->add($value);
        }
    }

    /**
     * Benchmark immutable remove operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableRemove(array $params): void
    {
        $list = $this->immutableList;
        foreach ($this->bulkData as $value) {
            $list = $list->remove($value);
        }
    }

    /**
     * Benchmark mutable remove operation for comparison
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchMutableRemove(array $params): void
    {
        $list = clone $this->mutableList;
        foreach ($this->bulkData as $value) {
            $list->remove($value);
        }
    }

    /**
     * Benchmark immutable bulk operations
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableAddAll(array $params): void
    {
        $this->immutableList->addAll($this->bulkData);
    }

    /**
     * Benchmark mutable bulk operations for comparison
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchMutableAddAll(array $params): void
    {
        $list = clone $this->mutableList;
        $list->addAll($this->bulkData);
    }

    /**
     * Benchmark withComparator operation (changing sort order)
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchWithComparator(array $params): void
    {
        $reverseComparator = new ReverseComparator(new NumericComparator());
        $this->immutableList->withComparator($reverseComparator);
    }

    /**
     * Benchmark structural sharing efficiency with small changes
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchStructuralSharingSmallChange(array $params): void
    {
        $list1 = $this->immutableList;
        $list2 = $list1->add(99999); // Add single element
        $list3 = $list2->remove(99999); // Remove same element

        // Access to ensure lists are realized
        $list1->size();
        $list2->size();
        $list3->size();
    }

    /**
     * Benchmark memory efficiency of multiple versions
     * @ParamProviders("provideDataSizes")
     * @Revs(50)
     * @Iterations(3)
     * @Warmup(1)
     */
    public function benchMultipleVersions(array $params): void
    {
        $versions = [];
        $list = $this->immutableList;

        // Create 10 versions with small changes
        for ($i = 0; $i < 10; $i++) {
            $list = $list->add(90000 + $i);
            $versions[] = $list;
        }

        // Access all versions to ensure they're realized
        foreach ($versions as $version) {
            $version->size();
        }
    }

    /**
     * Benchmark map operation on immutable list
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableMap(array $params): void
    {
        $this->immutableList->map(function ($value) {
            return $value * 2;
        });
    }

    /**
     * Benchmark filter operation on immutable list
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableFilter(array $params): void
    {
        $this->immutableList->filter(function ($value) {
            return $value % 2 === 0;
        });
    }

    /**
     * Benchmark chained operations on immutable list
     * @ParamProviders("provideDataSizes")
     * @Revs(50)
     * @Iterations(3)
     * @Warmup(1)
     */
    public function benchImmutableChainedOperations(array $params): void
    {
        $list = $this->immutableList
            ->addAll($this->bulkData)
            ->filter(function ($value) {
                return $value > 5000;
            })
            ->map(function ($value) {
                return $value * 2;
            });

        $list->toArray(); // Force evaluation
    }

    /**
     * Benchmark iteration on immutable list
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableIteration(array $params): void
    {
        $sum = 0;
        foreach ($this->immutableList as $value) {
            $sum += $value;
        }
    }

    /**
     * Benchmark clear operation (creates empty list)
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchImmutableClear(array $params): void
    {
        $this->immutableList->clear();
    }

    /**
     * Benchmark thread safety simulation (concurrent-like operations)
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchThreadSafetySimulation(array $params): void
    {
        $original = $this->immutableList;

        // Simulate multiple "threads" working on the same list
        $thread1Result = $original->add(111111)->add(222222);
        $thread2Result = $original->remove($this->testData[0])->add(333333);
        $thread3Result = $original->filter(function ($value) {
            return $value < 5000;
        });

        // All should have different results but share structure
        $thread1Result->size();
        $thread2Result->size();
        $thread3Result->size();
    }
}
