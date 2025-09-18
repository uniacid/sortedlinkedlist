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
 * @Groups({"iteration", "comparison"})
 */
class IterationBench
{
    private IntegerSortedLinkedList $integerList;
    private StringSortedLinkedList $stringList;
    private FloatSortedLinkedList $floatList;
    private array $integerArray = [];
    private array $stringArray = [];
    private array $floatArray = [];

    public function setUp(array $params): void
    {
        $size = $params['size'];

        // Initialize lists and arrays
        $this->integerList = new IntegerSortedLinkedList();
        $this->stringList = new StringSortedLinkedList();
        $this->floatList = new FloatSortedLinkedList();
        $this->integerArray = [];
        $this->stringArray = [];
        $this->floatArray = [];

        // Fill with data
        for ($i = 0; $i < $size; $i++) {
            $intValue = $i;
            $stringValue = sprintf('string_%010d', $i);
            $floatValue = $i * 1.1;

            $this->integerList->add($intValue);
            $this->integerArray[] = $intValue;

            $this->stringList->add($stringValue);
            $this->stringArray[] = $stringValue;

            $this->floatList->add($floatValue);
            $this->floatArray[] = $floatValue;
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
            'xxlarge' => ['size' => 10000],
        ];
    }

    /**
     * Benchmark forward iteration using Iterator interface
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListForwardIteration(array $params): void
    {
        $sum = 0;
        foreach ($this->integerList as $key => $value) {
            $sum += $value;
        }
    }

    /**
     * Benchmark native array foreach iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayForeach(array $params): void
    {
        $sum = 0;
        foreach ($this->integerArray as $key => $value) {
            $sum += $value;
        }
    }

    /**
     * Benchmark native array for loop iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayForLoop(array $params): void
    {
        $sum = 0;
        $count = count($this->integerArray);
        for ($i = 0; $i < $count; $i++) {
            $sum += $this->integerArray[$i];
        }
    }

    /**
     * Benchmark backward iteration using bidirectional iterator
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIntegerListBackwardIteration(array $params): void
    {
        $sum = 0;
        // Move to end
        $this->integerList->end();

        // Iterate backward
        do {
            $sum += $this->integerList->current();
        } while ($this->integerList->prev() !== false);

        // Reset iterator
        $this->integerList->rewind();
    }

    /**
     * Benchmark native array reverse iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeArrayReverse(array $params): void
    {
        $sum = 0;
        $reversed = array_reverse($this->integerArray);
        foreach ($reversed as $value) {
            $sum += $value;
        }
    }

    /**
     * Benchmark string list iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchStringListIteration(array $params): void
    {
        $concat = '';
        foreach ($this->stringList as $value) {
            $concat .= substr($value, 0, 1);
        }
    }

    /**
     * Benchmark native string array iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchNativeStringArrayIteration(array $params): void
    {
        $concat = '';
        foreach ($this->stringArray as $value) {
            $concat .= substr($value, 0, 1);
        }
    }

    /**
     * Benchmark float list iteration
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchFloatListIteration(array $params): void
    {
        $sum = 0.0;
        foreach ($this->floatList as $value) {
            $sum += $value;
        }
    }

    /**
     * Benchmark array access using ArrayAccess interface
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchArrayAccessInterface(array $params): void
    {
        $sum = 0;
        $size = $this->integerList->size();
        for ($i = 0; $i < $size; $i++) {
            $sum += $this->integerList[$i];
        }
    }

    /**
     * Benchmark manual iteration using next() and valid()
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchManualIteratorMethods(array $params): void
    {
        $sum = 0;
        $this->integerList->rewind();
        while ($this->integerList->valid()) {
            $sum += $this->integerList->current();
            $this->integerList->next();
        }
    }

    /**
     * Benchmark iteration with key access
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchIterationWithKey(array $params): void
    {
        $keySum = 0;
        $valueSum = 0;
        foreach ($this->integerList as $key => $value) {
            $keySum += $key;
            $valueSum += $value;
        }
    }

    /**
     * Benchmark partial iteration (first 10%)
     * @ParamProviders("provideDataSizes")
     * @Revs(100)
     * @Iterations(5)
     * @Warmup(2)
     */
    public function benchPartialIteration(array $params): void
    {
        $sum = 0;
        $limit = (int) ($params['size'] * 0.1);
        $count = 0;

        foreach ($this->integerList as $value) {
            if ($count >= $limit) {
                break;
            }
            $sum += $value;
            $count++;
        }
    }

    /**
     * Benchmark nested iteration (cartesian product simulation)
     * Note: Only for small sizes to avoid timeout
     * @ParamProviders("provideSmallSizes")
     * @Revs(10)
     * @Iterations(3)
     * @Warmup(1)
     */
    public function benchNestedIteration(array $params): void
    {
        $sum = 0;
        $limit = min(10, $params['size']); // Limit to prevent timeout

        foreach ($this->integerList as $outerValue) {
            if ($sum > $limit) {
                break;
            }
            foreach ($this->integerList as $innerValue) {
                if ($sum > $limit) {
                    break;
                }
                $sum++;
            }
        }
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function provideSmallSizes(): array
    {
        return [
            'tiny' => ['size' => 10],
            'small' => ['size' => 50],
        ];
    }
}