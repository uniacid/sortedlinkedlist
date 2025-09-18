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
 * @Groups({"search", "comparison"})
 */
class SearchOperationsBench
{
    private IntegerSortedLinkedList $integerList;
    private StringSortedLinkedList $stringList;
    private array $integerArray = [];
    private array $stringArray = [];
    private array $searchTargets = [];
    private array $stringSearchTargets = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];

        // Initialize lists
        $this->integerList = new IntegerSortedLinkedList();
        $this->stringList = new StringSortedLinkedList();
        $this->integerArray = [];
        $this->stringArray = [];

        // Fill with sequential data for predictable performance
        for ($i = 0; $i < $size; $i++) {
            $this->integerList->add($i);
            $this->integerArray[] = $i;

            $string = sprintf('%010d', $i);
            $this->stringList->add($string);
            $this->stringArray[] = $string;
        }

        // Generate search targets (mix of existing and non-existing values)
        $this->searchTargets = [];
        $this->stringSearchTargets = [];

        // Search for values at different positions
        if ($size > 0) {
            // First element (best case)
            $this->searchTargets[] = 0;
            $this->stringSearchTargets[] = sprintf('%010d', 0);

            // Middle element
            $middleIndex = (int) ($size / 2);
            $this->searchTargets[] = $middleIndex;
            $this->stringSearchTargets[] = sprintf('%010d', $middleIndex);

            // Last element (worst case for linear search)
            $this->searchTargets[] = $size - 1;
            $this->stringSearchTargets[] = sprintf('%010d', $size - 1);

            // Non-existing values
            $this->searchTargets[] = -1; // Before first
            $this->searchTargets[] = $size; // After last

            $this->stringSearchTargets[] = sprintf('%010d', -1);
            $this->stringSearchTargets[] = sprintf('%010d', $size);
        }
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function provideDataSizes(): array
    {
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
     * Benchmark binary search in SortedLinkedList
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchBinarySearchInteger(array $params): void
    {
        foreach ($this->searchTargets as $target) {
            $this->integerList->binarySearch($target);
        }
    }

    /**
     * Benchmark linear search (contains method) in SortedLinkedList
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchLinearSearchInteger(array $params): void
    {
        foreach ($this->searchTargets as $target) {
            $this->integerList->contains($target);
        }
    }

    /**
     * Benchmark native PHP in_array
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeInArray(array $params): void
    {
        foreach ($this->searchTargets as $target) {
            in_array($target, $this->integerArray, true);
        }
    }

    /**
     * Benchmark native PHP array_search
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArraySearch(array $params): void
    {
        foreach ($this->searchTargets as $target) {
            array_search($target, $this->integerArray, true);
        }
    }

    /**
     * Benchmark binary search for strings
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchBinarySearchString(array $params): void
    {
        foreach ($this->stringSearchTargets as $target) {
            $this->stringList->binarySearch($target);
        }
    }

    /**
     * Benchmark linear search for strings
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchLinearSearchString(array $params): void
    {
        foreach ($this->stringSearchTargets as $target) {
            $this->stringList->contains($target);
        }
    }

    /**
     * Benchmark worst case: searching for last element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchWorstCaseLinearSearch(array $params): void
    {
        $size = $params['size'];
        if ($size > 0) {
            $this->integerList->contains($size - 1);
        }
    }

    /**
     * Benchmark worst case for binary search: searching for last element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchWorstCaseBinarySearch(array $params): void
    {
        $size = $params['size'];
        if ($size > 0) {
            $this->integerList->binarySearch($size - 1);
        }
    }

    /**
     * Benchmark best case: searching for first element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchBestCaseLinearSearch(array $params): void
    {
        $size = $params['size'];
        if ($size > 0) {
            $this->integerList->contains(0);
        }
    }

    /**
     * Benchmark best case for binary search: searching for middle element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchBestCaseBinarySearch(array $params): void
    {
        $size = $params['size'];
        if ($size > 0) {
            $middle = (int) ($size / 2);
            $this->integerList->binarySearch($middle);
        }
    }

    /**
     * Benchmark search for non-existent element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchSearchNonExistentBinary(array $params): void
    {
        $this->integerList->binarySearch(-999999);
    }

    /**
     * Benchmark linear search for non-existent element
     * @ParamProviders("provideDataSizes")
     * @Revs(1000)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchSearchNonExistentLinear(array $params): void
    {
        $this->integerList->contains(-999999);
    }
}
