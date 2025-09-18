<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class IteratorTest extends TestCase
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

    public function testIteratorWithEmptyList(): void
    {
        $items = [];
        foreach ($this->intList as $key => $value) {
            $items[$key] = $value;
        }
        $this->assertEmpty($items);
    }

    public function testIteratorWithSingleElement(): void
    {
        $this->intList->add(42);
        $items = [];
        foreach ($this->intList as $key => $value) {
            $items[$key] = $value;
        }
        $this->assertEquals([0 => 42], $items);
    }

    public function testIteratorWithMultipleIntegers(): void
    {
        $this->intList->add(3);
        $this->intList->add(1);
        $this->intList->add(4);
        $this->intList->add(2);

        $items = [];
        foreach ($this->intList as $key => $value) {
            $items[$key] = $value;
        }

        $this->assertEquals([
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4
        ], $items);
    }

    public function testIteratorWithStrings(): void
    {
        $this->stringList->add("banana");
        $this->stringList->add("apple");
        $this->stringList->add("cherry");

        $items = [];
        foreach ($this->stringList as $key => $value) {
            $items[$key] = $value;
        }

        $this->assertEquals([
            0 => "apple",
            1 => "banana",
            2 => "cherry"
        ], $items);
    }

    public function testIteratorWithFloats(): void
    {
        $this->floatList->add(3.14);
        $this->floatList->add(1.41);
        $this->floatList->add(2.71);

        $items = [];
        foreach ($this->floatList as $key => $value) {
            $items[$key] = $value;
        }

        $this->assertEquals([
            0 => 1.41,
            1 => 2.71,
            2 => 3.14
        ], $items);
    }

    public function testRewindMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        // First iteration
        $firstRun = [];
        foreach ($this->intList as $value) {
            $firstRun[] = $value;
        }

        // Second iteration should work after automatic rewind
        $secondRun = [];
        foreach ($this->intList as $value) {
            $secondRun[] = $value;
        }

        $this->assertEquals($firstRun, $secondRun);
    }

    public function testCurrentMethod(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);

        $this->intList->rewind();
        $this->assertEquals(10, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(20, $this->intList->current());
    }

    public function testKeyMethod(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        $this->intList->rewind();
        $this->assertEquals(0, $this->intList->key());

        $this->intList->next();
        $this->assertEquals(1, $this->intList->key());

        $this->intList->next();
        $this->assertEquals(2, $this->intList->key());
    }

    public function testNextMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->rewind();
        $this->assertEquals(1, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(2, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(3, $this->intList->current());

        $this->intList->next();
        $this->assertFalse($this->intList->valid());
    }

    public function testValidMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);

        $this->intList->rewind();
        $this->assertTrue($this->intList->valid());

        $this->intList->next();
        $this->assertTrue($this->intList->valid());

        $this->intList->next();
        $this->assertFalse($this->intList->valid());
    }

    public function testIteratorAfterRemoval(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->remove(2);

        $items = [];
        foreach ($this->intList as $value) {
            $items[] = $value;
        }

        $this->assertEquals([1, 3], $items);
    }

    public function testIteratorAfterClear(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->clear();

        $items = [];
        foreach ($this->intList as $value) {
            $items[] = $value;
        }

        $this->assertEmpty($items);
    }

    public function testMultipleIterationsWithModifications(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);

        // First iteration
        $first = [];
        foreach ($this->intList as $value) {
            $first[] = $value;
        }
        $this->assertEquals([1, 2], $first);

        // Modify list
        $this->intList->add(3);
        $this->intList->remove(1);

        // Second iteration
        $second = [];
        foreach ($this->intList as $value) {
            $second[] = $value;
        }
        $this->assertEquals([2, 3], $second);
    }

    public function testIteratorWithDuplicates(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(1);
        $this->intList->add(2);

        $items = [];
        foreach ($this->intList as $value) {
            $items[] = $value;
        }

        $this->assertEquals([1, 1, 2, 2], $items);
    }
}