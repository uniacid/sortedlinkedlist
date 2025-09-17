<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\FloatSortedLinkedList;
use InvalidArgumentException;

class FloatSortedLinkedListTest extends TestCase
{
    private FloatSortedLinkedList $list;

    protected function setUp(): void
    {
        $this->list = new FloatSortedLinkedList();
    }

    public function testEmptyListHasSizeZero(): void
    {
        $this->assertEquals(0, $this->list->size());
    }

    public function testAddSingleFloat(): void
    {
        $this->list->add(3.14);
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(3.14));
    }

    public function testAddMultipleFloatsInOrder(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);
        $this->list->add(3.3);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(1.1));
        $this->assertTrue($this->list->contains(2.2));
        $this->assertTrue($this->list->contains(3.3));
    }

    public function testAddMultipleFloatsOutOfOrder(): void
    {
        $this->list->add(9.9);
        $this->list->add(1.1);
        $this->list->add(5.5);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(9.9));
        $this->assertTrue($this->list->contains(1.1));
        $this->assertTrue($this->list->contains(5.5));
    }

    public function testAddDuplicateFloats(): void
    {
        $this->list->add(2.718);
        $this->list->add(2.718);
        $this->list->add(2.718);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(2.718));
    }

    public function testAddNegativeFloats(): void
    {
        $this->list->add(-3.5);
        $this->list->add(-10.75);
        $this->list->add(-1.25);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(-3.5));
        $this->assertTrue($this->list->contains(-10.75));
        $this->assertTrue($this->list->contains(-1.25));
    }

    public function testAddMixedPositiveNegativeFloats(): void
    {
        $this->list->add(10.5);
        $this->list->add(-5.5);
        $this->list->add(0.0);
        $this->list->add(3.75);
        $this->list->add(-2.25);

        $this->assertEquals(5, $this->list->size());
        $this->assertTrue($this->list->contains(10.5));
        $this->assertTrue($this->list->contains(-5.5));
        $this->assertTrue($this->list->contains(0.0));
        $this->assertTrue($this->list->contains(3.75));
        $this->assertTrue($this->list->contains(-2.25));
    }

    public function testAddZeroFloat(): void
    {
        $this->list->add(0.0);
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(0.0));
    }

    public function testAddVerySmallFloats(): void
    {
        $this->list->add(0.000001);
        $this->list->add(0.0000001);
        $this->list->add(0.00000001);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(0.000001));
        $this->assertTrue($this->list->contains(0.0000001));
        $this->assertTrue($this->list->contains(0.00000001));
    }

    public function testAddVeryLargeFloats(): void
    {
        $this->list->add(1e10);
        $this->list->add(1e15);
        $this->list->add(1e20);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(1e10));
        $this->assertTrue($this->list->contains(1e15));
        $this->assertTrue($this->list->contains(1e20));
    }

    public function testAddFloatsWithPrecisionDifferences(): void
    {
        $this->list->add(0.1 + 0.2); // 0.30000000000000004 in IEEE 754
        $this->list->add(0.3);

        $this->assertEquals(2, $this->list->size());
        // Note: Due to floating point precision, these might not be exactly equal
        // But our contains method should handle this appropriately
    }

    public function testAddInfinityValues(): void
    {
        $this->list->add(INF);
        $this->list->add(-INF);
        $this->list->add(1.0);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(INF));
        $this->assertTrue($this->list->contains(-INF));
        $this->assertTrue($this->list->contains(1.0));
    }

    public function testAddNaNThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NaN values are not allowed');

        $this->list->add(NAN);
    }

    public function testRemoveFromEmptyList(): void
    {
        $this->assertFalse($this->list->remove(3.14));
        $this->assertEquals(0, $this->list->size());
    }

    public function testRemoveExistingFloat(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);
        $this->list->add(3.3);

        $this->assertTrue($this->list->remove(2.2));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(2.2));
        $this->assertTrue($this->list->contains(1.1));
        $this->assertTrue($this->list->contains(3.3));
    }

    public function testRemoveFirstFloat(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);
        $this->list->add(3.3);

        $this->assertTrue($this->list->remove(1.1));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(1.1));
    }

    public function testRemoveLastFloat(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);
        $this->list->add(3.3);

        $this->assertTrue($this->list->remove(3.3));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(3.3));
    }

    public function testRemoveNonExistentFloat(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);

        $this->assertFalse($this->list->remove(5.5));
        $this->assertEquals(2, $this->list->size());
    }

    public function testRemoveOnlyFloat(): void
    {
        $this->list->add(42.0);

        $this->assertTrue($this->list->remove(42.0));
        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains(42.0));
    }

    public function testRemoveZero(): void
    {
        $this->list->add(-1.0);
        $this->list->add(0.0);
        $this->list->add(1.0);

        $this->assertTrue($this->list->remove(0.0));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(0.0));
        $this->assertTrue($this->list->contains(-1.0));
        $this->assertTrue($this->list->contains(1.0));
    }

    public function testRemoveNegativeFloat(): void
    {
        $this->list->add(-5.5);
        $this->list->add(0.0);
        $this->list->add(5.5);

        $this->assertTrue($this->list->remove(-5.5));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(-5.5));
    }

    public function testRemoveFirstOccurrenceOfDuplicate(): void
    {
        $this->list->add(7.77);
        $this->list->add(7.77);
        $this->list->add(7.77);

        $this->assertTrue($this->list->remove(7.77));
        $this->assertEquals(2, $this->list->size());
        $this->assertTrue($this->list->contains(7.77));
    }

    public function testRemoveInfinity(): void
    {
        $this->list->add(-INF);
        $this->list->add(0.0);
        $this->list->add(INF);

        $this->assertTrue($this->list->remove(INF));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains(INF));
        $this->assertTrue($this->list->contains(-INF));
    }

    public function testContainsOnEmptyList(): void
    {
        $this->assertFalse($this->list->contains(1.23));
    }

    public function testContainsExistingFloat(): void
    {
        $this->list->add(2.718);
        $this->list->add(3.14159);

        $this->assertTrue($this->list->contains(2.718));
        $this->assertTrue($this->list->contains(3.14159));
    }

    public function testContainsNonExistentFloat(): void
    {
        $this->list->add(2.718);
        $this->list->add(3.14159);

        $this->assertFalse($this->list->contains(1.414));
    }

    public function testContainsWithPrecision(): void
    {
        $this->list->add(0.1 + 0.2);

        // Test that we can find values considering floating point precision
        $this->assertTrue($this->list->contains(0.1 + 0.2));
    }

    public function testClearEmptyList(): void
    {
        $this->list->clear();
        $this->assertEquals(0, $this->list->size());
    }

    public function testClearNonEmptyList(): void
    {
        $this->list->add(1.1);
        $this->list->add(2.2);
        $this->list->add(3.3);

        $this->list->clear();

        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains(1.1));
        $this->assertFalse($this->list->contains(2.2));
        $this->assertFalse($this->list->contains(3.3));
    }

    public function testAddAfterClear(): void
    {
        $this->list->add(1.1);
        $this->list->clear();
        $this->list->add(5.5);

        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(5.5));
        $this->assertFalse($this->list->contains(1.1));
    }

    public function testAddIntegerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float');

        $this->list->add(123);
    }

    public function testAddStringThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float');

        $this->list->add("3.14");
    }

    public function testAddBooleanThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float');

        $this->list->add(true);
    }

    public function testAddNullThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float');

        $this->list->add(null);
    }

    public function testAddArrayThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float');

        $this->list->add([1.1, 2.2, 3.3]);
    }

    public function testScientificNotation(): void
    {
        $this->list->add(1.23e-5);
        $this->list->add(4.56e3);
        $this->list->add(7.89e-10);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains(1.23e-5));
        $this->assertTrue($this->list->contains(4.56e3));
        $this->assertTrue($this->list->contains(7.89e-10));
    }

    public function testLargeDataset(): void
    {
        $values = [];
        for ($i = 0; $i < 1000; $i++) {
            $values[] = $i + ($i / 1000.0);
        }

        shuffle($values);

        foreach ($values as $value) {
            $this->list->add($value);
        }

        $this->assertEquals(1000, $this->list->size());

        // Verify all values are present
        foreach ($values as $value) {
            $this->assertTrue($this->list->contains($value), "Value $value should be in the list");
        }
    }

    public function testStressTestWithRandomOperations(): void
    {
        $operations = 5000;
        $values = [];

        for ($i = 0; $i < $operations; $i++) {
            // Generate random float between -1000 and 1000
            $value = (rand(0, 2000000) - 1000000) / 1000.0;

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

    public function testFloatPrecisionComparison(): void
    {
        // Test that very close but different floats are handled correctly
        $this->list->add(0.1234567890123456);
        $this->list->add(0.1234567890123457);

        $this->assertEquals(2, $this->list->size());

        // Both values should be present as they are different
        $this->assertTrue($this->list->contains(0.1234567890123456));
        $this->assertTrue($this->list->contains(0.1234567890123457));
    }
}