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
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;

/**
 * @BeforeMethods("setUp")
 * @OutputTimeUnit("microseconds", precision=3)
 * @Groups({"bulk", "comparison"})
 */
class BulkOperationsBench
{
    private IntegerSortedLinkedList $integerList;
    private StringSortedLinkedList $stringList;
    private array $integerArray = [];
    private array $stringArray = [];
    private array $bulkData = [];
    private array $bulkStringData = [];
    private array $removeData = [];
    private array $retainData = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];
        $bulkSize = $params['bulk_size'] ?? (int) ($size * 0.5);

        // Initialize lists and arrays with base data
        $this->integerList = new IntegerSortedLinkedList();
        $this->stringList = new StringSortedLinkedList();
        $this->integerArray = [];
        $this->stringArray = [];

        // Fill with initial data
        for ($i = 0; $i < $size; $i += 2) {
            $this->integerList->add($i);
            $this->integerArray[] = $i;

            $stringValue = sprintf('string_%010d', $i);
            $this->stringList->add($stringValue);
            $this->stringArray[] = $stringValue;
        }

        // Prepare bulk data for addition (mix of new and existing values)
        $this->bulkData = [];
        $this->bulkStringData = [];
        for ($i = 0; $i < $bulkSize; $i++) {
            $this->bulkData[] = $i * 3; // Some will overlap, some won't
            $this->bulkStringData[] = sprintf('string_%010d', $i * 3);
        }

        // Prepare data for removal (50% of existing elements)
        $this->removeData = [];
        for ($i = 0; $i < $size; $i += 4) {
            $this->removeData[] = $i;
        }

        // Prepare data for retain (keep only even elements from original)
        $this->retainData = [];
        for ($i = 0; $i < $size; $i += 4) {
            $this->retainData[] = $i;
        }
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function provideDataSizes(): array
    {
        return [
            'small' => ['size' => 100, 'bulk_size' => 50],
            'medium' => ['size' => 500, 'bulk_size' => 250],
            'large' => ['size' => 1000, 'bulk_size' => 500],
            'xlarge' => ['size' => 5000, 'bulk_size' => 2500],
        ];
    }

    /**
     * Benchmark addAll operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListAddAll(array $params): void
    {
        $list = clone $this->integerList;
        $list->addAll($this->bulkData);
    }

    /**
     * Benchmark native array merge and sort
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayMergeSort(array $params): void
    {
        $array = $this->integerArray;
        $array = array_merge($array, $this->bulkData);
        $array = array_unique($array); // Remove duplicates like SortedLinkedList does
        sort($array);
    }

    /**
     * Benchmark removeAll operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListRemoveAll(array $params): void
    {
        $list = clone $this->integerList;
        $list->removeAll($this->removeData);
    }

    /**
     * Benchmark native array diff
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayDiff(array $params): void
    {
        $array = $this->integerArray;
        $array = array_diff($array, $this->removeData);
        $array = array_values($array); // Re-index
    }

    /**
     * Benchmark retainAll operation (intersection)
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListRetainAll(array $params): void
    {
        $list = clone $this->integerList;
        $list->retainAll($this->retainData);
    }

    /**
     * Benchmark native array intersect
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayIntersect(array $params): void
    {
        $array = $this->integerArray;
        $array = array_intersect($array, $this->retainData);
        $array = array_values($array); // Re-index
    }

    /**
     * Benchmark containsAll operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListContainsAll(array $params): void
    {
        $this->integerList->containsAll($this->retainData);
    }

    /**
     * Benchmark native array containsAll equivalent
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayContainsAll(array $params): void
    {
        $allFound = true;
        foreach ($this->retainData as $value) {
            if (!in_array($value, $this->integerArray, true)) {
                $allFound = false;
                break;
            }
        }
    }

    /**
     * Benchmark toArray operation
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListToArray(array $params): void
    {
        $this->integerList->toArray();
    }

    /**
     * Benchmark fromArray operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListFromArray(array $params): void
    {
        IntegerSortedLinkedList::fromArray($this->bulkData);
    }

    /**
     * Benchmark map operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListMap(array $params): void
    {
        $this->integerList->map(function ($value) {
            return $value * 2;
        });
    }

    /**
     * Benchmark native array_map
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayMap(array $params): void
    {
        array_map(function ($value) {
            return $value * 2;
        }, $this->integerArray);
    }

    /**
     * Benchmark filter operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListFilter(array $params): void
    {
        $this->integerList->filter(function ($value) {
            return $value % 2 === 0;
        });
    }

    /**
     * Benchmark native array_filter
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayFilter(array $params): void
    {
        $filtered = array_filter($this->integerArray, function ($value) {
            return $value % 2 === 0;
        });
        array_values($filtered); // Re-index
    }

    /**
     * Benchmark reduce operation
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListReduce(array $params): void
    {
        $this->integerList->reduce(function ($carry, $value) {
            return $carry + $value;
        }, 0);
    }

    /**
     * Benchmark native array_reduce
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayReduce(array $params): void
    {
        array_reduce($this->integerArray, function ($carry, $value) {
            return $carry + $value;
        }, 0);
    }

    /**
     * Benchmark string list bulk operations
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchStringListAddAll(array $params): void
    {
        $list = clone $this->stringList;
        $list->addAll($this->bulkStringData);
    }

    /**
     * Benchmark complex bulk operation chain
     * @ParamProviders("provideDataSizes")
     * @Revs(50)
     * @Iterations(3)
     * @Warmup(1)
     */
    public function benchChainedBulkOperations(array $params): void
    {
        $list = clone $this->integerList;
        $list->addAll($this->bulkData);
        $filtered = $list->filter(function ($value) {
            return $value % 3 === 0;
        });
        $mapped = $filtered->map(function ($value) {
            return $value * 2;
        });
        $mapped->toArray();
    }
}