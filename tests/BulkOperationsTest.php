<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class BulkOperationsTest extends TestCase
{
    private IntegerSortedLinkedList $intList;
    private StringSortedLinkedList $stringList;
    private FloatSortedLinkedList $floatList;

    protected function setUp(): void
    {
        $this->intList = new IntegerSortedLinkedList();
        $this->stringList = new StringSortedLinkedList();
        $this->floatList = new FloatSortedLinkedList();
    }

    // Tests for addAll method

    public function testAddAllWithEmptyArray(): void
    {
        $this->intList->addAll([]);
        $this->assertEquals(0, $this->intList->size());
    }

    public function testAddAllWithIntegerArray(): void
    {
        $values = [5, 2, 8, 1, 9];
        $this->intList->addAll($values);

        $this->assertEquals(5, $this->intList->size());
        foreach ($values as $value) {
            $this->assertTrue($this->intList->contains($value));
        }

        // Verify sorted order
        $expected = [1, 2, 5, 8, 9];
        $this->assertEquals($expected, $this->intList->toArray());
    }

    public function testAddAllWithDuplicates(): void
    {
        $this->intList->add(3);
        $this->intList->addAll([1, 2, 3, 4, 3]);

        $this->assertEquals(6, $this->intList->size());
        $expected = [1, 2, 3, 3, 3, 4];
        $this->assertEquals($expected, $this->intList->toArray());
    }

    public function testAddAllWithIterable(): void
    {
        $generator = function() {
            yield 3;
            yield 1;
            yield 2;
        };

        $this->intList->addAll($generator());
        $this->assertEquals(3, $this->intList->size());
        $this->assertEquals([1, 2, 3], $this->intList->toArray());
    }

    public function testAddAllWithStringArray(): void
    {
        $values = ['zebra', 'apple', 'mango', 'banana'];
        $this->stringList->addAll($values);

        $this->assertEquals(4, $this->stringList->size());
        $expected = ['apple', 'banana', 'mango', 'zebra'];
        $this->assertEquals($expected, $this->stringList->toArray());
    }

    public function testAddAllToNonEmptyList(): void
    {
        $this->intList->add(5);
        $this->intList->add(10);
        $this->intList->addAll([3, 7, 12]);

        $this->assertEquals(5, $this->intList->size());
        $this->assertEquals([3, 5, 7, 10, 12], $this->intList->toArray());
    }

    // Tests for removeAll method

    public function testRemoveAllWithEmptyArray(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $this->intList->removeAll([]);
        $this->assertEquals(5, $this->intList->size());
    }

    public function testRemoveAllWithMatchingValues(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $this->intList->removeAll([2, 4]);

        $this->assertEquals(3, $this->intList->size());
        $this->assertEquals([1, 3, 5], $this->intList->toArray());
    }

    public function testRemoveAllWithNonExistentValues(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $this->intList->removeAll([4, 5, 6]);

        $this->assertEquals(3, $this->intList->size());
        $this->assertEquals([1, 2, 3], $this->intList->toArray());
    }

    public function testRemoveAllWithDuplicates(): void
    {
        $this->intList->addAll([1, 2, 2, 3, 3, 3, 4]);
        $this->intList->removeAll([2, 3]);

        $this->assertEquals(2, $this->intList->size());
        $this->assertEquals([1, 4], $this->intList->toArray());
    }

    public function testRemoveAllFromEmptyList(): void
    {
        $this->intList->removeAll([1, 2, 3]);
        $this->assertEquals(0, $this->intList->size());
    }

    // Tests for retainAll method

    public function testRetainAllWithEmptyArray(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $this->intList->retainAll([]);
        $this->assertEquals(0, $this->intList->size());
    }

    public function testRetainAllWithMatchingValues(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $this->intList->retainAll([2, 3, 5, 7]);

        $this->assertEquals(3, $this->intList->size());
        $this->assertEquals([2, 3, 5], $this->intList->toArray());
    }

    public function testRetainAllWithNoIntersection(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $this->intList->retainAll([4, 5, 6]);

        $this->assertEquals(0, $this->intList->size());
    }

    public function testRetainAllWithDuplicates(): void
    {
        $this->intList->addAll([1, 2, 2, 3, 3, 3, 4]);
        $this->intList->retainAll([2, 3]);

        $this->assertEquals(5, $this->intList->size());
        $this->assertEquals([2, 2, 3, 3, 3], $this->intList->toArray());
    }

    public function testRetainAllWithAllValues(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $this->intList->retainAll([1, 2, 3, 4, 5]);

        $this->assertEquals(3, $this->intList->size());
        $this->assertEquals([1, 2, 3], $this->intList->toArray());
    }

    // Tests for containsAll method

    public function testContainsAllWithEmptyArray(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $this->assertTrue($this->intList->containsAll([]));
    }

    public function testContainsAllWithMatchingValues(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $this->assertTrue($this->intList->containsAll([2, 3, 5]));
        $this->assertTrue($this->intList->containsAll([1, 2, 3, 4, 5]));
    }

    public function testContainsAllWithMissingValues(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $this->assertFalse($this->intList->containsAll([2, 3, 4]));
        $this->assertFalse($this->intList->containsAll([4, 5, 6]));
    }

    public function testContainsAllWithDuplicates(): void
    {
        $this->intList->addAll([1, 2, 2, 3]);
        $this->assertTrue($this->intList->containsAll([2, 2, 3]));
    }

    public function testContainsAllOnEmptyList(): void
    {
        $this->assertFalse($this->intList->containsAll([1, 2, 3]));
        $this->assertTrue($this->intList->containsAll([]));
    }

    // Tests for toArray and fromArray methods

    public function testToArrayOnEmptyList(): void
    {
        $this->assertEquals([], $this->intList->toArray());
    }

    public function testToArrayWithIntegers(): void
    {
        $this->intList->addAll([3, 1, 4, 1, 5]);
        $this->assertEquals([1, 1, 3, 4, 5], $this->intList->toArray());
    }

    public function testToArrayWithStrings(): void
    {
        $this->stringList->addAll(['dog', 'cat', 'bird']);
        $this->assertEquals(['bird', 'cat', 'dog'], $this->stringList->toArray());
    }

    public function testToArrayWithFloats(): void
    {
        $this->floatList->addAll([3.14, 1.5, 2.7]);
        $this->assertEquals([1.5, 2.7, 3.14], $this->floatList->toArray());
    }

    public function testFromArrayWithEmptyArray(): void
    {
        $list = IntegerSortedLinkedList::fromArray([]);
        $this->assertEquals(0, $list->size());
    }

    public function testFromArrayWithIntegers(): void
    {
        $values = [5, 2, 8, 1, 9];
        $list = IntegerSortedLinkedList::fromArray($values);

        $this->assertEquals(5, $list->size());
        $this->assertEquals([1, 2, 5, 8, 9], $list->toArray());
    }

    public function testFromArrayWithStrings(): void
    {
        $values = ['zebra', 'apple', 'mango'];
        $list = StringSortedLinkedList::fromArray($values);

        $this->assertEquals(3, $list->size());
        $this->assertEquals(['apple', 'mango', 'zebra'], $list->toArray());
    }

    public function testFromArrayWithDuplicates(): void
    {
        $values = [3, 1, 2, 1, 3];
        $list = IntegerSortedLinkedList::fromArray($values);

        $this->assertEquals(5, $list->size());
        $this->assertEquals([1, 1, 2, 3, 3], $list->toArray());
    }

    // Tests for map method

    public function testMapOnEmptyList(): void
    {
        $result = $this->intList->map(fn($x) => $x * 2);
        $this->assertInstanceOf(IntegerSortedLinkedList::class, $result);
        $this->assertEquals(0, $result->size());
    }

    public function testMapWithIntegerTransformation(): void
    {
        $this->intList->addAll([1, 2, 3, 4]);
        $result = $this->intList->map(fn($x) => $x * 2);

        $this->assertInstanceOf(IntegerSortedLinkedList::class, $result);
        $this->assertEquals([2, 4, 6, 8], $result->toArray());
        // Original list should be unchanged
        $this->assertEquals([1, 2, 3, 4], $this->intList->toArray());
    }

    public function testMapWithStringTransformation(): void
    {
        $this->stringList->addAll(['apple', 'banana', 'cherry']);
        $result = $this->stringList->map(fn($s) => strtoupper($s));

        $this->assertEquals(['APPLE', 'BANANA', 'CHERRY'], $result->toArray());
        $this->assertEquals(['apple', 'banana', 'cherry'], $this->stringList->toArray());
    }

    public function testMapMaintainsSortedOrder(): void
    {
        $this->intList->addAll([1, 2, 3, 4]);
        // Reverse transformation that would break order
        $result = $this->intList->map(fn($x) => 10 - $x);

        // Result should be re-sorted
        $this->assertEquals([6, 7, 8, 9], $result->toArray());
    }

    // Tests for filter method

    public function testFilterOnEmptyList(): void
    {
        $result = $this->intList->filter(fn($x) => $x > 0);
        $this->assertInstanceOf(IntegerSortedLinkedList::class, $result);
        $this->assertEquals(0, $result->size());
    }

    public function testFilterWithPredicate(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5, 6]);
        $result = $this->intList->filter(fn($x) => $x % 2 === 0);

        $this->assertEquals([2, 4, 6], $result->toArray());
        // Original unchanged
        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->intList->toArray());
    }

    public function testFilterRemovesAll(): void
    {
        $this->intList->addAll([1, 3, 5, 7]);
        $result = $this->intList->filter(fn($x) => $x % 2 === 0);

        $this->assertEquals(0, $result->size());
        $this->assertEquals([], $result->toArray());
    }

    public function testFilterKeepsAll(): void
    {
        $this->intList->addAll([2, 4, 6, 8]);
        $result = $this->intList->filter(fn($x) => $x % 2 === 0);

        $this->assertEquals([2, 4, 6, 8], $result->toArray());
    }

    public function testFilterWithStrings(): void
    {
        $this->stringList->addAll(['apple', 'banana', 'apricot', 'cherry']);
        $result = $this->stringList->filter(fn($s) => str_starts_with($s, 'a'));

        $this->assertEquals(['apple', 'apricot'], $result->toArray());
    }

    // Tests for reduce method

    public function testReduceOnEmptyList(): void
    {
        $result = $this->intList->reduce(fn($acc, $val) => $acc + $val, 0);
        $this->assertEquals(0, $result);
    }

    public function testReduceSum(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);
        $result = $this->intList->reduce(fn($acc, $val) => $acc + $val, 0);

        $this->assertEquals(15, $result);
    }

    public function testReduceProduct(): void
    {
        $this->intList->addAll([1, 2, 3, 4]);
        $result = $this->intList->reduce(fn($acc, $val) => $acc * $val, 1);

        $this->assertEquals(24, $result);
    }

    public function testReduceStringConcatenation(): void
    {
        $this->stringList->addAll(['a', 'b', 'c']);
        $result = $this->stringList->reduce(fn($acc, $val) => $acc . $val, '');

        $this->assertEquals('abc', $result);
    }

    public function testReduceMax(): void
    {
        $this->intList->addAll([3, 7, 2, 9, 1]);
        $result = $this->intList->reduce(
            fn($max, $val) => $val > $max ? $val : $max,
            PHP_INT_MIN
        );

        $this->assertEquals(9, $result);
    }

    public function testReduceWithDifferentReturnType(): void
    {
        $this->intList->addAll([1, 2, 3]);
        $result = $this->intList->reduce(
            fn($acc, $val) => $acc . ',' . $val,
            ''
        );

        $this->assertEquals(',1,2,3', $result);
    }

    // Integration tests

    public function testChainedBulkOperations(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $this->intList->removeAll([2, 4, 6, 8, 10]);

        $this->assertEquals([1, 3, 5, 7, 9], $this->intList->toArray());

        $this->intList->retainAll([3, 5, 7, 11, 13]);
        $this->assertEquals([3, 5, 7], $this->intList->toArray());
    }

    public function testChainedTransformations(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5]);

        $result = $this->intList
            ->filter(fn($x) => $x % 2 === 1)  // [1, 3, 5]
            ->map(fn($x) => $x * 2);           // [2, 6, 10]

        $this->assertEquals([2, 6, 10], $result->toArray());
    }

    public function testComplexTransformationPipeline(): void
    {
        $this->intList->addAll([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $evenSum = $this->intList
            ->filter(fn($x) => $x % 2 === 0)
            ->map(fn($x) => $x * $x)
            ->reduce(fn($acc, $val) => $acc + $val, 0);

        // 2^2 + 4^2 + 6^2 + 8^2 + 10^2 = 4 + 16 + 36 + 64 + 100 = 220
        $this->assertEquals(220, $evenSum);
    }

    public function testBulkOperationsPerformance(): void
    {
        $largeArray = range(1, 1000);
        shuffle($largeArray);

        $startTime = microtime(true);
        $this->intList->addAll($largeArray);
        $duration = microtime(true) - $startTime;

        $this->assertEquals(1000, $this->intList->size());
        $this->assertLessThan(1.0, $duration, 'Bulk insertion took too long');

        // Verify sorted
        $result = $this->intList->toArray();
        $this->assertEquals(range(1, 1000), $result);
    }
}