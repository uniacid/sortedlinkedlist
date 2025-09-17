<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\Node;

/**
 * Concrete implementation for testing the abstract SortedLinkedList class
 */
class TestSortedLinkedList extends SortedLinkedList
{
    /**
     * Compare two values for sorting.
     * This test implementation uses integer comparison.
     */
    protected function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }

    /**
     * Helper method to get the head node for testing
     */
    public function getHead(): ?Node
    {
        return $this->head;
    }
}

/**
 * @covers \SortedLinkedList\SortedLinkedList
 */
class SortedLinkedListTest extends TestCase
{
    private TestSortedLinkedList $list;

    protected function setUp(): void
    {
        $this->list = new TestSortedLinkedList();
    }

    public function testInitiallyEmpty(): void
    {
        $this->assertEquals(0, $this->list->size());
        $this->assertNull($this->list->getHead());
    }

    public function testAddSingleElement(): void
    {
        $this->list->add(42);

        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(42));
        $this->assertNotNull($this->list->getHead());
        $this->assertEquals(42, $this->list->getHead()->getValue());
    }

    public function testAddMultipleElementsSorted(): void
    {
        $this->list->add(30);
        $this->list->add(10);
        $this->list->add(20);

        $this->assertEquals(3, $this->list->size());

        // Verify sorted order
        $node = $this->list->getHead();
        $this->assertEquals(10, $node->getValue());
        $node = $node->getNext();
        $this->assertEquals(20, $node->getValue());
        $node = $node->getNext();
        $this->assertEquals(30, $node->getValue());
        $this->assertNull($node->getNext());
    }

    public function testAddDuplicateValues(): void
    {
        $this->list->add(25);
        $this->list->add(25);
        $this->list->add(25);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(25));

        // All three should be in the list
        $node = $this->list->getHead();
        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals(25, $node->getValue());
            $node = $node->getNext();
        }
        $this->assertNull($node);
    }

    public function testContainsExistingValue(): void
    {
        $this->list->add(10);
        $this->list->add(20);
        $this->list->add(30);

        $this->assertTrue($this->list->contains(10));
        $this->assertTrue($this->list->contains(20));
        $this->assertTrue($this->list->contains(30));
    }

    public function testContainsNonExistingValue(): void
    {
        $this->list->add(10);
        $this->list->add(20);

        $this->assertFalse($this->list->contains(15));
        $this->assertFalse($this->list->contains(0));
        $this->assertFalse($this->list->contains(100));
    }

    public function testContainsOnEmptyList(): void
    {
        $this->assertFalse($this->list->contains(42));
    }

    public function testRemoveFromMiddle(): void
    {
        $this->list->add(10);
        $this->list->add(20);
        $this->list->add(30);

        $result = $this->list->remove(20);

        $this->assertTrue($result);
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(20));
        $this->assertTrue($this->list->contains(10));
        $this->assertTrue($this->list->contains(30));
    }

    public function testRemoveFirstElement(): void
    {
        $this->list->add(10);
        $this->list->add(20);
        $this->list->add(30);

        $result = $this->list->remove(10);

        $this->assertTrue($result);
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(10));
        $this->assertEquals(20, $this->list->getHead()->getValue());
    }

    public function testRemoveLastElement(): void
    {
        $this->list->add(10);
        $this->list->add(20);
        $this->list->add(30);

        $result = $this->list->remove(30);

        $this->assertTrue($result);
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(30));

        // Verify last element is now 20
        $node = $this->list->getHead();
        while ($node->getNext() !== null) {
            $node = $node->getNext();
        }
        $this->assertEquals(20, $node->getValue());
    }

    public function testRemoveSingleElement(): void
    {
        $this->list->add(42);

        $result = $this->list->remove(42);

        $this->assertTrue($result);
        $this->assertEquals(0, $this->list->size());
        $this->assertNull($this->list->getHead());
        $this->assertFalse($this->list->contains(42));
    }

    public function testRemoveNonExistingValue(): void
    {
        $this->list->add(10);
        $this->list->add(20);

        $result = $this->list->remove(30);

        $this->assertFalse($result);
        $this->assertEquals(2, $this->list->size());
        $this->assertTrue($this->list->contains(10));
        $this->assertTrue($this->list->contains(20));
    }

    public function testRemoveFromEmptyList(): void
    {
        $result = $this->list->remove(42);

        $this->assertFalse($result);
        $this->assertEquals(0, $this->list->size());
    }

    public function testRemoveOneDuplicate(): void
    {
        $this->list->add(25);
        $this->list->add(25);
        $this->list->add(25);

        $result = $this->list->remove(25);

        $this->assertTrue($result);
        $this->assertEquals(2, $this->list->size());
        $this->assertTrue($this->list->contains(25));
    }

    public function testClear(): void
    {
        $this->list->add(10);
        $this->list->add(20);
        $this->list->add(30);

        $this->list->clear();

        $this->assertEquals(0, $this->list->size());
        $this->assertNull($this->list->getHead());
        $this->assertFalse($this->list->contains(10));
        $this->assertFalse($this->list->contains(20));
        $this->assertFalse($this->list->contains(30));
    }

    public function testClearEmptyList(): void
    {
        $this->list->clear();

        $this->assertEquals(0, $this->list->size());
        $this->assertNull($this->list->getHead());
    }

    public function testSizeAfterOperations(): void
    {
        $this->assertEquals(0, $this->list->size());

        $this->list->add(10);
        $this->assertEquals(1, $this->list->size());

        $this->list->add(20);
        $this->assertEquals(2, $this->list->size());

        $this->list->add(30);
        $this->assertEquals(3, $this->list->size());

        $this->list->remove(20);
        $this->assertEquals(2, $this->list->size());

        $this->list->clear();
        $this->assertEquals(0, $this->list->size());
    }

    public function testLargeDataSet(): void
    {
        // Add 100 random numbers
        $values = [];
        for ($i = 0; $i < 100; $i++) {
            $value = rand(1, 1000);
            $values[] = $value;
            $this->list->add($value);
        }

        $this->assertEquals(100, $this->list->size());

        // Verify all values are present
        foreach ($values as $value) {
            $this->assertTrue($this->list->contains($value));
        }

        // Verify sorted order
        $prev = null;
        $node = $this->list->getHead();
        while ($node !== null) {
            if ($prev !== null) {
                $this->assertGreaterThanOrEqual($prev, $node->getValue());
            }
            $prev = $node->getValue();
            $node = $node->getNext();
        }
    }

    public function testAddNegativeNumbers(): void
    {
        $this->list->add(-10);
        $this->list->add(5);
        $this->list->add(-20);
        $this->list->add(0);

        $this->assertEquals(4, $this->list->size());

        // Verify sorted order: -20, -10, 0, 5
        $node = $this->list->getHead();
        $this->assertEquals(-20, $node->getValue());
        $node = $node->getNext();
        $this->assertEquals(-10, $node->getValue());
        $node = $node->getNext();
        $this->assertEquals(0, $node->getValue());
        $node = $node->getNext();
        $this->assertEquals(5, $node->getValue());
    }

    public function testMixedOperations(): void
    {
        // Add some values
        $this->list->add(50);
        $this->list->add(30);
        $this->list->add(70);
        $this->list->add(20);
        $this->list->add(40);

        $this->assertEquals(5, $this->list->size());

        // Remove some values
        $this->list->remove(30);
        $this->list->remove(70);

        $this->assertEquals(3, $this->list->size());

        // Add more values
        $this->list->add(10);
        $this->list->add(60);

        $this->assertEquals(5, $this->list->size());

        // Verify final sorted order: 10, 20, 40, 50, 60
        $expected = [10, 20, 40, 50, 60];
        $node = $this->list->getHead();
        foreach ($expected as $value) {
            $this->assertEquals($value, $node->getValue());
            $node = $node->getNext();
        }
        $this->assertNull($node);
    }
}