<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerImmutableSortedLinkedList;
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\Comparator\NumericComparator;
use SortedLinkedList\Comparator\StringComparator;

class ImmutableSortedLinkedListTest extends TestCase
{
    /**
     * Test 4.1: ImmutableSortedLinkedList constructor
     */
    public function testConstructor(): void
    {
        $list = new IntegerImmutableSortedLinkedList();
        $this->assertInstanceOf(ImmutableSortedLinkedList::class, $list);
        $this->assertEquals(0, $list->size());
        $this->assertNull($list->getComparator());
    }

    public function testConstructorWithComparator(): void
    {
        $comparator = new NumericComparator();
        $list = new IntegerImmutableSortedLinkedList($comparator);
        $this->assertInstanceOf(ImmutableSortedLinkedList::class, $list);
        $this->assertEquals(0, $list->size());
        $this->assertSame($comparator, $list->getComparator());
    }

    /**
     * Test 4.2: Immutable add operation (returns new instance)
     */
    public function testAddReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5);

        $this->assertNotSame($list1, $list2);
        $this->assertEquals(0, $list1->size());
        $this->assertEquals(1, $list2->size());
        $this->assertTrue($list2->contains(5));
        $this->assertFalse($list1->contains(5));
    }

    public function testMultipleAddsReturnNewInstances(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(3);
        $list3 = $list2->withAdd(1);
        $list4 = $list3->withAdd(5);

        $this->assertNotSame($list1, $list2);
        $this->assertNotSame($list2, $list3);
        $this->assertNotSame($list3, $list4);

        $this->assertEquals(0, $list1->size());
        $this->assertEquals(1, $list2->size());
        $this->assertEquals(2, $list3->size());
        $this->assertEquals(3, $list4->size());

        $this->assertEquals([1, 3, 5], $list4->toArray());
    }

    /**
     * Test 4.3: Immutable remove operation
     */
    public function testRemoveReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5)->withAdd(3)->withAdd(7);
        $list3 = $list2->withRemove(3);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals(3, $list2->size());
        $this->assertEquals(2, $list3->size());
        $this->assertTrue($list2->contains(3));
        $this->assertFalse($list3->contains(3));
        $this->assertEquals([3, 5, 7], $list2->toArray());
        $this->assertEquals([5, 7], $list3->toArray());
    }

    public function testRemoveNonExistentReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5);
        $list3 = $list2->withRemove(10);

        // Even when removing non-existent, returns new instance
        $this->assertNotSame($list2, $list3);
        $this->assertEquals(1, $list2->size());
        $this->assertEquals(1, $list3->size());
        $this->assertEquals([5], $list3->toArray());
    }

    /**
     * Test 4.4: Structural sharing verification
     */
    public function testStructuralSharingOnAdd(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5)->withAdd(10)->withAdd(15);
        $list3 = $list2->withAdd(3); // Add at beginning

        // Verify both lists work correctly
        $this->assertEquals([5, 10, 15], $list2->toArray());
        $this->assertEquals([3, 5, 10, 15], $list3->toArray());

        // List2's structure should remain unchanged
        $this->assertEquals(3, $list2->size());
        $this->assertFalse($list2->contains(3));
    }

    public function testStructuralSharingOnRemove(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5)->withAdd(10)->withAdd(15)->withAdd(20);
        $list3 = $list2->withRemove(10);

        // Verify both lists work correctly
        $this->assertEquals([5, 10, 15, 20], $list2->toArray());
        $this->assertEquals([5, 15, 20], $list3->toArray());

        // Original list remains unchanged
        $this->assertTrue($list2->contains(10));
        $this->assertFalse($list3->contains(10));
    }

    /**
     * Test 4.5: Thread safety guarantees
     * PHP doesn't have true threads, but we test immutability guarantees
     */
    public function testImmutabilityGuarantees(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5)->withAdd(10);

        // Store original state
        $originalArray = $list2->toArray();
        $originalSize = $list2->size();

        // Perform multiple operations
        $list3 = $list2->withAdd(15);
        $list4 = $list2->withRemove(5);
        $list5 = $list2->withAdd(7);

        // Original list remains unchanged
        $this->assertEquals($originalArray, $list2->toArray());
        $this->assertEquals($originalSize, $list2->size());

        // Each derivative has its own state
        $this->assertEquals([5, 10, 15], $list3->toArray());
        $this->assertEquals([10], $list4->toArray());
        $this->assertEquals([5, 7, 10], $list5->toArray());
    }

    /**
     * Test bulk operations return new instances
     */
    public function testAddAllReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAdd(5);
        $list3 = $list2->withAddAll([3, 7, 1]);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals(1, $list2->size());
        $this->assertEquals(4, $list3->size());
        $this->assertEquals([5], $list2->toArray());
        $this->assertEquals([1, 3, 5, 7], $list3->toArray());
    }

    public function testRemoveAllReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([1, 3, 5, 7, 9]);
        $list3 = $list2->withRemoveAll([3, 7]);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals(5, $list2->size());
        $this->assertEquals(3, $list3->size());
        $this->assertEquals([1, 3, 5, 7, 9], $list2->toArray());
        $this->assertEquals([1, 5, 9], $list3->toArray());
    }

    public function testRetainAllReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([1, 3, 5, 7, 9]);
        $list3 = $list2->withRetainAll([3, 5, 11]);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals(5, $list2->size());
        $this->assertEquals(2, $list3->size());
        $this->assertEquals([1, 3, 5, 7, 9], $list2->toArray());
        $this->assertEquals([3, 5], $list3->toArray());
    }

    /**
     * Test clear returns new empty instance
     */
    public function testClearReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([1, 3, 5]);
        $list3 = $list2->withClear();

        $this->assertNotSame($list2, $list3);
        $this->assertEquals(3, $list2->size());
        $this->assertEquals(0, $list3->size());
        $this->assertEquals([1, 3, 5], $list2->toArray());
        $this->assertEquals([], $list3->toArray());
    }

    /**
     * Test withComparator method
     */
    public function testWithComparator(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([3, 1, 5, 2, 4]);

        // Create reverse comparator
        $reverseComparator = new class implements \SortedLinkedList\Comparator\ComparatorInterface {
            public function compare(mixed $a, mixed $b): int {
                return $b <=> $a; // Reverse order
            }
        };

        $list3 = $list2->withComparator($reverseComparator);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals([1, 2, 3, 4, 5], $list2->toArray());
        $this->assertEquals([5, 4, 3, 2, 1], $list3->toArray());

        // Original list unchanged
        $this->assertNull($list2->getComparator());
        $this->assertSame($reverseComparator, $list3->getComparator());
    }

    /**
     * Test transformation methods return new instances
     */
    public function testMapReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([1, 2, 3]);
        $list3 = $list2->map(fn($x) => $x * 2);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals([1, 2, 3], $list2->toArray());
        $this->assertEquals([2, 4, 6], $list3->toArray());
    }

    public function testFilterReturnsNewInstance(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withAddAll([1, 2, 3, 4, 5]);
        $list3 = $list2->filter(fn($x) => $x % 2 === 0);

        $this->assertNotSame($list2, $list3);
        $this->assertEquals([1, 2, 3, 4, 5], $list2->toArray());
        $this->assertEquals([2, 4], $list3->toArray());
    }

    /**
     * Test that mutating operations throw exceptions
     */
    public function testAddThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withAdd() instead.');
        $list->add(5);
    }

    public function testRemoveThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();
        $list = $list->withAdd(5);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withRemove() instead.');
        $list->remove(5);
    }

    public function testClearThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withClear() instead.');
        $list->clear();
    }

    public function testAddAllThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withAddAll() instead.');
        $list->addAll([1, 2, 3]);
    }

    public function testRemoveAllThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withRemoveAll() instead.');
        $list->removeAll([1, 2]);
    }

    public function testRetainAllThrowsException(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Cannot mutate immutable list. Use withRetainAll() instead.');
        $list->retainAll([1, 2]);
    }

    /**
     * Test chaining operations
     */
    public function testMethodChaining(): void
    {
        $list = new IntegerImmutableSortedLinkedList();

        $result = $list
            ->withAdd(5)
            ->withAdd(3)
            ->withAdd(7)
            ->withAdd(1)
            ->withRemove(3)
            ->withAdd(9);

        $this->assertEquals([1, 5, 7, 9], $result->toArray());
        $this->assertEquals(4, $result->size());
    }

    /**
     * Test empty list operations
     */
    public function testEmptyListOperations(): void
    {
        $list1 = new IntegerImmutableSortedLinkedList();
        $list2 = $list1->withRemove(5);
        $list3 = $list1->withClear();
        $list4 = $list1->withRemoveAll([1, 2, 3]);

        $this->assertEquals(0, $list1->size());
        $this->assertEquals(0, $list2->size());
        $this->assertEquals(0, $list3->size());
        $this->assertEquals(0, $list4->size());

        // All should be new instances
        $this->assertNotSame($list1, $list2);
        $this->assertNotSame($list1, $list3);
        $this->assertNotSame($list1, $list4);
    }

    /**
     * Test with custom types
     */
    public function testWithCustomTypes(): void
    {
        $comparator = new StringComparator();
        $list1 = ImmutableSortedLinkedList::create($comparator);

        $list2 = $list1
            ->withAdd('banana')
            ->withAdd('apple')
            ->withAdd('cherry');

        $this->assertEquals(['apple', 'banana', 'cherry'], $list2->toArray());

        $list3 = $list2->withRemove('banana');
        $this->assertEquals(['apple', 'cherry'], $list3->toArray());
        $this->assertEquals(['apple', 'banana', 'cherry'], $list2->toArray());
    }

    /**
     * Test static factory method
     */
    public function testFromArrayCreatesImmutableList(): void
    {
        $list = IntegerImmutableSortedLinkedList::fromArray([3, 1, 4, 1, 5, 9]);

        $this->assertInstanceOf(ImmutableSortedLinkedList::class, $list);
        $this->assertEquals([1, 1, 3, 4, 5, 9], $list->toArray());

        // Verify it's truly immutable
        $this->expectException(\BadMethodCallException::class);
        $list->add(2);
    }
}