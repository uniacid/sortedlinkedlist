<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use InvalidArgumentException;

class IntegerSortedLinkedListTest extends TestCase
{
    private IntegerSortedLinkedList $list;

    protected function setUp(): void
    {
        $this->list = new IntegerSortedLinkedList();
    }

    public function testEmptyListHasSizeZero(): void
    {
        $this->assertEquals(0, $this->list->size());
    }

    public function testAddSingleInteger(): void
    {
        $this->list->add(42);
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(42));
    }

    public function testAddMultipleIntegersInOrder(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(1));
        $this->assertTrue($this->list->contains(2));
        $this->assertTrue($this->list->contains(3));
    }

    public function testAddMultipleIntegersOutOfOrder(): void
    {
        $this->list->add(3);
        $this->list->add(1);
        $this->list->add(2);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(1));
        $this->assertTrue($this->list->contains(2));
        $this->assertTrue($this->list->contains(3));
    }

    public function testAddDuplicateIntegers(): void
    {
        $this->list->add(5);
        $this->list->add(5);
        $this->list->add(5);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(5));
    }

    public function testAddNegativeIntegers(): void
    {
        $this->list->add(-5);
        $this->list->add(-10);
        $this->list->add(-1);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(-5));
        $this->assertTrue($this->list->contains(-10));
        $this->assertTrue($this->list->contains(-1));
    }

    public function testAddMixedPositiveNegativeIntegers(): void
    {
        $this->list->add(10);
        $this->list->add(-5);
        $this->list->add(0);
        $this->list->add(3);
        $this->list->add(-2);

        $this->assertEquals(5, $this->list->size());
        $this->assertTrue($this->list->contains(10));
        $this->assertTrue($this->list->contains(-5));
        $this->assertTrue($this->list->contains(0));
        $this->assertTrue($this->list->contains(3));
        $this->assertTrue($this->list->contains(-2));
    }

    public function testAddZero(): void
    {
        $this->list->add(0);
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(0));
    }

    public function testAddLargeIntegers(): void
    {
        $this->list->add(PHP_INT_MAX);
        $this->list->add(PHP_INT_MIN);
        $this->list->add(0);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(PHP_INT_MAX));
        $this->assertTrue($this->list->contains(PHP_INT_MIN));
        $this->assertTrue($this->list->contains(0));
    }

    public function testRemoveFromEmptyList(): void
    {
        $this->assertFalse($this->list->remove(5));
        $this->assertEquals(0, $this->list->size());
    }

    public function testRemoveExistingInteger(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        $this->assertTrue($this->list->remove(2));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(2));
        $this->assertTrue($this->list->contains(1));
        $this->assertTrue($this->list->contains(3));
    }

    public function testRemoveFirstInteger(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        $this->assertTrue($this->list->remove(1));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(1));
    }

    public function testRemoveLastInteger(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        $this->assertTrue($this->list->remove(3));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(3));
    }

    public function testRemoveNonExistentInteger(): void
    {
        $this->list->add(1);
        $this->list->add(2);

        $this->assertFalse($this->list->remove(5));
        $this->assertEquals(2, $this->list->size());
    }

    public function testRemoveOnlyInteger(): void
    {
        $this->list->add(42);

        $this->assertTrue($this->list->remove(42));
        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains(42));
    }

    public function testRemoveFirstOccurrenceOfDuplicate(): void
    {
        $this->list->add(5);
        $this->list->add(5);
        $this->list->add(5);

        $this->assertTrue($this->list->remove(5));
        $this->assertEquals(2, $this->list->size());
        $this->assertTrue($this->list->contains(5));
    }

    public function testContainsOnEmptyList(): void
    {
        $this->assertFalse($this->list->contains(1));
    }

    public function testContainsExistingInteger(): void
    {
        $this->list->add(5);
        $this->list->add(10);

        $this->assertTrue($this->list->contains(5));
        $this->assertTrue($this->list->contains(10));
    }

    public function testContainsNonExistentInteger(): void
    {
        $this->list->add(5);
        $this->list->add(10);

        $this->assertFalse($this->list->contains(7));
    }

    public function testClearEmptyList(): void
    {
        $this->list->clear();
        $this->assertEquals(0, $this->list->size());
    }

    public function testClearNonEmptyList(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        $this->list->clear();

        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains(1));
        $this->assertFalse($this->list->contains(2));
        $this->assertFalse($this->list->contains(3));
    }

    public function testAddAfterClear(): void
    {
        $this->list->add(1);
        $this->list->clear();
        $this->list->add(5);

        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(5));
        $this->assertFalse($this->list->contains(1));
    }

    public function testAddNonIntegerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        $this->list->add("string");
    }

    public function testAddFloatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        $this->list->add(3.14);
    }

    public function testAddBooleanThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        $this->list->add(true);
    }

    public function testAddNullThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        $this->list->add(null);
    }

    public function testAddArrayThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        $this->list->add([1, 2, 3]);
    }

    public function testLargeDataset(): void
    {
        $values = range(1, 1000);
        shuffle($values);

        foreach ($values as $value) {
            $this->list->add($value);
        }

        $this->assertEquals(1000, $this->list->size());

        // Verify all values are present
        for ($i = 1; $i <= 1000; $i++) {
            $this->assertTrue($this->list->contains($i), "Value $i should be in the list");
        }
    }

    public function testStressTestWithRandomOperations(): void
    {
        $operations = 10000;
        $values = [];

        for ($i = 0; $i < $operations; $i++) {
            $value = rand(-1000, 1000);

            if (rand(0, 1) === 0) {
                // Add operation
                $this->list->add($value);
                $values[] = $value;
            } else {
                // Remove operation
                if (!empty($values)) {
                    $indexToRemove = array_rand($values);
                    $valueToRemove = $values[$indexToRemove];
                    if ($this->list->remove($valueToRemove)) {
                        unset($values[$indexToRemove]);
                        $values = array_values($values);
                    }
                }
            }
        }

        $this->assertEquals(count($values), $this->list->size());
    }

    public function testAddWithWrongTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        // @phpstan-ignore-next-line
        $this->list->add("not an integer");
    }

    public function testAddWithFloatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer');

        // @phpstan-ignore-next-line
        $this->list->add(3.14);
    }

    public function testRemoveWithWrongTypeReturnsFalse(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove("2"));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(2.0));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(null));

        $this->assertEquals(3, $this->list->size());
    }

    public function testContainsWithWrongTypeReturnsFalse(): void
    {
        $this->list->add(1);
        $this->list->add(2);
        $this->list->add(3);

        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains("1"));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(1.0));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(null));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(true));

        $this->assertTrue($this->list->contains(1));
        $this->assertTrue($this->list->contains(2));
    }
}
