<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class BinarySearchTest extends TestCase
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

    public function testBinarySearchEmptyList(): void
    {
        $this->assertEquals(-1, $this->intList->binarySearch(5));
    }

    public function testBinarySearchSingleElement(): void
    {
        $this->intList->add(10);
        $this->assertEquals(0, $this->intList->binarySearch(10));
        $this->assertEquals(-1, $this->intList->binarySearch(5));
        $this->assertEquals(-1, $this->intList->binarySearch(15));
    }

    public function testBinarySearchIntegers(): void
    {
        // Add numbers in random order
        $this->intList->add(5);
        $this->intList->add(2);
        $this->intList->add(8);
        $this->intList->add(1);
        $this->intList->add(9);
        $this->intList->add(3);
        $this->intList->add(7);
        $this->intList->add(4);
        $this->intList->add(6);

        // List should be sorted: [1, 2, 3, 4, 5, 6, 7, 8, 9]
        $this->assertEquals(0, $this->intList->binarySearch(1));
        $this->assertEquals(1, $this->intList->binarySearch(2));
        $this->assertEquals(2, $this->intList->binarySearch(3));
        $this->assertEquals(3, $this->intList->binarySearch(4));
        $this->assertEquals(4, $this->intList->binarySearch(5));
        $this->assertEquals(5, $this->intList->binarySearch(6));
        $this->assertEquals(6, $this->intList->binarySearch(7));
        $this->assertEquals(7, $this->intList->binarySearch(8));
        $this->assertEquals(8, $this->intList->binarySearch(9));

        // Test not found
        $this->assertEquals(-1, $this->intList->binarySearch(0));
        $this->assertEquals(-1, $this->intList->binarySearch(10));
    }

    public function testBinarySearchStrings(): void
    {
        $this->stringList->add("dog");
        $this->stringList->add("cat");
        $this->stringList->add("bird");
        $this->stringList->add("elephant");
        $this->stringList->add("ant");

        // Sorted: ["ant", "bird", "cat", "dog", "elephant"]
        $this->assertEquals(0, $this->stringList->binarySearch("ant"));
        $this->assertEquals(1, $this->stringList->binarySearch("bird"));
        $this->assertEquals(2, $this->stringList->binarySearch("cat"));
        $this->assertEquals(3, $this->stringList->binarySearch("dog"));
        $this->assertEquals(4, $this->stringList->binarySearch("elephant"));

        // Not found
        $this->assertEquals(-1, $this->stringList->binarySearch("zebra"));
        $this->assertEquals(-1, $this->stringList->binarySearch("aardvark"));
    }

    public function testBinarySearchFloats(): void
    {
        $this->floatList->add(3.14);
        $this->floatList->add(1.41);
        $this->floatList->add(2.71);
        $this->floatList->add(0.99);
        $this->floatList->add(5.55);

        // Sorted: [0.99, 1.41, 2.71, 3.14, 5.55]
        $this->assertEquals(0, $this->floatList->binarySearch(0.99));
        $this->assertEquals(1, $this->floatList->binarySearch(1.41));
        $this->assertEquals(2, $this->floatList->binarySearch(2.71));
        $this->assertEquals(3, $this->floatList->binarySearch(3.14));
        $this->assertEquals(4, $this->floatList->binarySearch(5.55));

        // Not found
        $this->assertEquals(-1, $this->floatList->binarySearch(0.5));
        $this->assertEquals(-1, $this->floatList->binarySearch(10.0));
    }

    public function testBinarySearchWithDuplicates(): void
    {
        $this->intList->add(3);
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);
        $this->intList->add(3);
        $this->intList->add(4);

        // Sorted: [1, 2, 3, 3, 3, 4]
        // Should find any valid position of 3 (indices 2, 3, or 4)
        $position = $this->intList->binarySearch(3);
        $this->assertTrue($position >= 2 && $position <= 4);

        // Verify the found position contains the correct value
        $this->assertEquals(3, $this->intList[$position]);
    }

    public function testBinarySearchLargeList(): void
    {
        // Add 1000 elements
        for ($i = 1; $i <= 1000; $i++) {
            $this->intList->add($i);
        }

        // Test finding various elements
        $this->assertEquals(0, $this->intList->binarySearch(1));
        $this->assertEquals(99, $this->intList->binarySearch(100));
        $this->assertEquals(499, $this->intList->binarySearch(500));
        $this->assertEquals(999, $this->intList->binarySearch(1000));

        // Test not found
        $this->assertEquals(-1, $this->intList->binarySearch(0));
        $this->assertEquals(-1, $this->intList->binarySearch(1001));
    }

    public function testBinarySearchAfterRemoval(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);
        $this->intList->add(4);
        $this->intList->add(5);

        $this->intList->remove(3);

        // List is now [1, 2, 4, 5]
        $this->assertEquals(0, $this->intList->binarySearch(1));
        $this->assertEquals(1, $this->intList->binarySearch(2));
        $this->assertEquals(-1, $this->intList->binarySearch(3)); // Removed
        $this->assertEquals(2, $this->intList->binarySearch(4));
        $this->assertEquals(3, $this->intList->binarySearch(5));
    }

    public function testBinarySearchAfterClear(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->clear();

        $this->assertEquals(-1, $this->intList->binarySearch(1));
        $this->assertEquals(-1, $this->intList->binarySearch(2));
        $this->assertEquals(-1, $this->intList->binarySearch(3));
    }

    public function testBinarySearchPerformance(): void
    {
        // Add a large number of elements
        for ($i = 0; $i < 10000; $i += 2) {
            $this->intList->add($i);
        }

        $startTime = microtime(true);

        // Perform multiple searches
        for ($i = 0; $i < 1000; $i++) {
            $searchValue = $i * 10;
            $this->intList->binarySearch($searchValue);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        // Binary search should be very fast, even for many searches
        // Should complete 1000 searches in less than 0.1 seconds
        $this->assertLessThan(0.1, $duration, "Binary search is too slow");
    }

    public function testIndexOfMethod(): void
    {
        $this->intList->add(3);
        $this->intList->add(1);
        $this->intList->add(4);
        $this->intList->add(2);

        // indexOf should use binary search internally
        $this->assertEquals(0, $this->intList->indexOf(1));
        $this->assertEquals(1, $this->intList->indexOf(2));
        $this->assertEquals(2, $this->intList->indexOf(3));
        $this->assertEquals(3, $this->intList->indexOf(4));
        $this->assertEquals(-1, $this->intList->indexOf(5));
    }
}