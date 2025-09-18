<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;

class BidirectionalIteratorTest extends TestCase
{
    private IntegerSortedLinkedList $intList;
    private StringSortedLinkedList $stringList;

    protected function setUp(): void
    {
        $this->intList = new IntegerSortedLinkedList();
        $this->stringList = new StringSortedLinkedList();
    }

    public function testPrevMethodFromMiddle(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->rewind();
        $this->intList->next(); // At position 1 (value 2)
        $this->intList->next(); // At position 2 (value 3)

        $this->assertEquals(3, $this->intList->current());

        $this->intList->prev(); // Back to position 1
        $this->assertEquals(2, $this->intList->current());

        $this->intList->prev(); // Back to position 0
        $this->assertEquals(1, $this->intList->current());
    }

    public function testPrevMethodAtBeginning(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);

        $this->intList->rewind();
        $this->assertEquals(1, $this->intList->current());

        // Calling prev at beginning should keep position at 0
        $this->intList->prev();
        $this->assertTrue($this->intList->valid());
        $this->assertEquals(1, $this->intList->current());
        $this->assertEquals(0, $this->intList->key());
    }

    public function testAlternatingNextAndPrev(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);
        $this->intList->add(40);

        $this->intList->rewind();
        $this->assertEquals(10, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(20, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(30, $this->intList->current());

        $this->intList->prev();
        $this->assertEquals(20, $this->intList->current());

        $this->intList->next();
        $this->assertEquals(30, $this->intList->current());

        $this->intList->prev();
        $this->intList->prev();
        $this->assertEquals(10, $this->intList->current());
    }

    public function testEndMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->end(); // Move to last element
        $this->assertEquals(3, $this->intList->current());
        $this->assertEquals(2, $this->intList->key());
    }

    public function testEndMethodEmptyList(): void
    {
        $this->intList->end();
        $this->assertFalse($this->intList->valid());
    }

    public function testEndMethodSingleElement(): void
    {
        $this->intList->add(42);

        $this->intList->end();
        $this->assertEquals(42, $this->intList->current());
        $this->assertEquals(0, $this->intList->key());
    }

    public function testReverseIteration(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);
        $this->intList->add(4);

        // Start from end and go backward
        $this->intList->end();
        $values = [];

        do {
            $values[] = $this->intList->current();
            $this->intList->prev();
        } while ($this->intList->key() > 0);

        // Get the first element
        $values[] = $this->intList->current();

        $this->assertEquals([4, 3, 2, 1], $values);
    }

    public function testSeekMethod(): void
    {
        $this->intList->add(10);
        $this->intList->add(20);
        $this->intList->add(30);
        $this->intList->add(40);
        $this->intList->add(50);

        // Seek to specific positions
        $this->intList->seek(2);
        $this->assertEquals(30, $this->intList->current());
        $this->assertEquals(2, $this->intList->key());

        $this->intList->seek(0);
        $this->assertEquals(10, $this->intList->current());
        $this->assertEquals(0, $this->intList->key());

        $this->intList->seek(4);
        $this->assertEquals(50, $this->intList->current());
        $this->assertEquals(4, $this->intList->key());
    }

    public function testSeekOutOfBounds(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);

        $this->expectException(\OutOfBoundsException::class);
        $this->intList->seek(5);
    }

    public function testSeekNegativePosition(): void
    {
        $this->intList->add(1);

        $this->expectException(\OutOfBoundsException::class);
        $this->intList->seek(-1);
    }

    public function testBidirectionalWithStrings(): void
    {
        $this->stringList->add("delta");
        $this->stringList->add("alpha");
        $this->stringList->add("charlie");
        $this->stringList->add("bravo");

        // Sorted: ["alpha", "bravo", "charlie", "delta"]
        $this->stringList->rewind();
        $this->assertEquals("alpha", $this->stringList->current());

        $this->stringList->next();
        $this->stringList->next();
        $this->assertEquals("charlie", $this->stringList->current());

        $this->stringList->prev();
        $this->assertEquals("bravo", $this->stringList->current());

        $this->stringList->end();
        $this->assertEquals("delta", $this->stringList->current());
    }

    public function testComplexNavigation(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->intList->add($i);
        }

        // Start at beginning
        $this->intList->rewind();
        $this->assertEquals(1, $this->intList->current());

        // Jump to middle
        $this->intList->seek(5);
        $this->assertEquals(6, $this->intList->current());

        // Go back two
        $this->intList->prev();
        $this->intList->prev();
        $this->assertEquals(4, $this->intList->current());

        // Jump to end
        $this->intList->end();
        $this->assertEquals(10, $this->intList->current());

        // Go back to start
        $this->intList->rewind();
        $this->assertEquals(1, $this->intList->current());
    }

    public function testBidirectionalAfterModification(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);
        $this->intList->add(4);

        $this->intList->seek(2); // At value 3
        $this->assertEquals(3, $this->intList->current());

        // Remove an element
        $this->intList->remove(2);

        // Position might need adjustment
        // List is now [1, 3, 4]
        $this->intList->rewind();
        $this->intList->next();
        $this->assertEquals(3, $this->intList->current());

        $this->intList->prev();
        $this->assertEquals(1, $this->intList->current());
    }

    public function testHasPrevMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->rewind();
        $this->assertFalse($this->intList->hasPrev()); // At position 0

        $this->intList->next();
        $this->assertTrue($this->intList->hasPrev()); // At position 1

        $this->intList->next();
        $this->assertTrue($this->intList->hasPrev()); // At position 2
    }

    public function testHasNextMethod(): void
    {
        $this->intList->add(1);
        $this->intList->add(2);
        $this->intList->add(3);

        $this->intList->rewind();
        $this->assertTrue($this->intList->hasNext()); // At position 0

        $this->intList->next();
        $this->assertTrue($this->intList->hasNext()); // At position 1

        $this->intList->next();
        $this->assertFalse($this->intList->hasNext()); // At position 2 (last)
    }
}