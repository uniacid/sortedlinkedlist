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
use SortedLinkedList\FloatSortedLinkedList;

/**
 * @BeforeMethods("setUp")
 * @OutputTimeUnit("microseconds", precision=3)
 * @Groups({"add", "comparison"})
 */
class AddOperationsBench
{
    private array $integerData = [];
    private array $stringData = [];
    private array $floatData = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];

        // Generate random data for benchmarks
        for ($i = 0; $i < $size; $i++) {
            $this->integerData[] = random_int(1, 100000);
            $this->stringData[] = bin2hex(random_bytes(10));
            $this->floatData[] = random_int(1, 100000) / 100.0;
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
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerSortedLinkedListAdd(array $params): void
    {
        $list = new IntegerSortedLinkedList();
        foreach ($this->integerData as $value) {
            $list->add($value);
        }
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayIntegerAdd(array $params): void
    {
        $array = [];
        foreach ($this->integerData as $value) {
            $array[] = $value;
            sort($array);
        }
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayIntegerOptimized(array $params): void
    {
        $array = [];
        foreach ($this->integerData as $value) {
            $array[] = $value;
        }
        sort($array);
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchStringSortedLinkedListAdd(array $params): void
    {
        $list = new StringSortedLinkedList();
        foreach ($this->stringData as $value) {
            $list->add($value);
        }
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayStringAdd(array $params): void
    {
        $array = [];
        foreach ($this->stringData as $value) {
            $array[] = $value;
            sort($array);
        }
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchFloatSortedLinkedListAdd(array $params): void
    {
        $list = new FloatSortedLinkedList();
        foreach ($this->floatData as $value) {
            $list->add($value);
        }
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayFloatAdd(array $params): void
    {
        $array = [];
        foreach ($this->floatData as $value) {
            $array[] = $value;
            sort($array, SORT_NUMERIC);
        }
    }

    /**
     * Benchmark for single insertions in already sorted data
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchSingleInsertionSortedList(array $params): void
    {
        $list = new IntegerSortedLinkedList();

        // Pre-fill list with sorted data
        for ($i = 0; $i < $params['size']; $i += 2) {
            $list->add($i);
        }

        // Insert in middle (worst case for sorted linked list)
        $list->add($params['size'] / 2 + 1);
    }

    /**
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchSingleInsertionNativeArray(array $params): void
    {
        $array = [];

        // Pre-fill array with sorted data
        for ($i = 0; $i < $params['size']; $i += 2) {
            $array[] = $i;
        }

        // Insert in middle
        $array[] = $params['size'] / 2 + 1;
        sort($array);
    }
}
