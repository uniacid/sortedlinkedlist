<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class ArrayAccessTest extends TestCase
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

    public function testOffsetExistsWithEmptyList(): void
    {
        $this->assertFalse(isset($this->intList[0]));
        $this->assertFalse(isset($this->intList[1]));
        $this->assertFalse(isset($this->intList[-1]));
    }

    public function testOffsetExistsWithElements(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        $this->assertTrue(isset($this->intList[0]));
        $this->assertTrue(isset($this->intList[1]));
        $this->assertTrue(isset($this->intList[2]));
        $this->assertFalse(isset($this->intList[3]));
        $this->assertFalse(isset($this->intList[-1]));
    }

    public function testOffsetGetWithIntegers(): void
    {
        $this->intList->add(30);
        $this->intList->add(10);
        $this->intList->add(20);

        // Should be sorted: [10, 20, 30]
        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(20, $this->intList[1]);
        $this->assertEquals(30, $this->intList[2]);
    }

    public function testOffsetGetWithStrings(): void
    {
        $this->stringList->add("charlie");
        $this->stringList->add("alice");
        $this->stringList->add("bob");

        // Should be sorted: ["alice", "bob", "charlie"]
        $this->assertEquals("alice", $this->stringList[0]);
        $this->assertEquals("bob", $this->stringList[1]);
        $this->assertEquals("charlie", $this->stringList[2]);
    }

    public function testOffsetGetWithFloats(): void
    {
        $this->floatList->add(3.14);
        $this->floatList->add(1.41);
        $this->floatList->add(2.71);

        // Should be sorted: [1.41, 2.71, 3.14]
        $this->assertEquals(1.41, $this->floatList[0]);
        $this->assertEquals(2.71, $this->floatList[1]);
        $this->assertEquals(3.14, $this->floatList[2]);
    }

    public function testOffsetGetOutOfBounds(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);

        $this->expectException(\OutOfBoundsException::class);
        $value = $this->intList[2];
    }

    public function testOffsetGetNegativeIndex(): void
    {
        $this->intList->add(10);

        $this->expectException(\OutOfBoundsException::class);
        $value = $this->intList[-1];
    }

    public function testOffsetSetAtEnd(): void
    {
        $this->intList->add(10);
        $this->intList->add(30);

        // Adding value 20 should maintain sort order
        $this->intList[] = 20;

        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(20, $this->intList[1]);
        $this->assertEquals(30, $this->intList[2]);
        $this->assertEquals(3, $this->intList->size());
    }

    public function testOffsetSetWithIndex(): void
    {
        $this->intList->add(10);
        $this->intList->add(30);

        // Setting with index should be ignored for sorting, value should be inserted in sorted position
        $this->intList[5] = 20; // Index ignored, 20 inserted in sorted position

        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(20, $this->intList[1]);
        $this->assertEquals(30, $this->intList[2]);
        $this->assertEquals(3, $this->intList->size());
    }

    public function testOffsetSetMaintainsSorting(): void
    {
        $this->intList->add(1);
        $this->intList->add(3);
        $this->intList->add(5);

        // Try to set at index 0, but value should go to correct sorted position
        $this->intList[0] = 4;

        // List should be [1, 3, 4, 5]
        $this->assertEquals(1, $this->intList[0]);
        $this->assertEquals(3, $this->intList[1]);
        $this->assertEquals(4, $this->intList[2]);
        $this->assertEquals(5, $this->intList[3]);
    }

    public function testOffsetUnsetFirstElement(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        unset($this->intList[0]);

        $this->assertEquals(2, $this->intList->size());
        $this->assertEquals(20, $this->intList[0]);
        $this->assertEquals(30, $this->intList[1]);
    }

    public function testOffsetUnsetMiddleElement(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        unset($this->intList[1]);

        $this->assertEquals(2, $this->intList->size());
        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(30, $this->intList[1]);
    }

    public function testOffsetUnsetLastElement(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        unset($this->intList[2]);

        $this->assertEquals(2, $this->intList->size());
        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(20, $this->intList[1]);
    }

    public function testOffsetUnsetOutOfBounds(): void
    {
        $this->intList->add(10);

        // Should not throw exception, just do nothing
        unset($this->intList[5]);
        $this->assertEquals(1, $this->intList->size());
    }

    public function testArrayAccessInLoop(): void
    {
        $this->intList->add(3);
        $this->intList->add(1);
        $this->intList->add(4);
        $this->intList->add(2);

        $sum = 0;
        for ($i = 0; $i < $this->intList->size(); $i++) {
            $sum += $this->intList[$i];
        }

        $this->assertEquals(10, $sum); // 1 + 2 + 3 + 4
    }

    public function testArrayAccessWithCount(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);

        // Using count() should work if Countable is implemented
        $count = count($this->intList);
        $this->assertEquals(3, $count);
    }

    public function testArrayAccessAfterRemoval(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);
        $this->intList->add(40);

        $this->intList->remove(20);

        // List is now [10, 30, 40]
        $this->assertEquals(10, $this->intList[0]);
        $this->assertEquals(30, $this->intList[1]);
        $this->assertEquals(40, $this->intList[2]);
        $this->assertFalse(isset($this->intList[3]));
    }

    public function testArrayAccessAfterClear(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);

        $this->intList->clear();

        $this->assertFalse(isset($this->intList[0]));
        $this->assertFalse(isset($this->intList[1]));
    }

    public function testMixedArrayAccessAndIterator(): void
    {
        $this->intList->add(3);
        $this->intList->add(1);
        $this->intList->add(2);

        // Use array access
        $this->assertEquals(1, $this->intList[0]);

        // Use iterator
        $items = [];
        foreach ($this->intList as $key => $value) {
            // Also use array access inside the loop
            $this->assertEquals($value, $this->intList[$key]);
            $items[] = $value;
        }

        $this->assertEquals([1, 2, 3], $items);
    }
}