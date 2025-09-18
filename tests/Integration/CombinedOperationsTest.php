<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class CombinedOperationsTest extends TestCase
{
    public function testCompleteWorkflowInteger(): void
    {
        $list = new IntegerSortedLinkedList();

        // Initial state
        $this->assertEquals(0, $list->size());
        $this->assertFalse($list->contains(5));

        // Add elements in random order
        $elements = [50, 10, 30, 20, 40, 25, 35, 15, 45, 5];
        foreach ($elements as $element) {
            $list->add($element);
        }

        // Verify size and contents
        $this->assertEquals(10, $list->size());
        foreach ($elements as $element) {
            $this->assertTrue($list->contains($element), "List should contain $element");
        }

        // Add duplicates
        $list->add(20);
        $list->add(30);
        $list->add(40);
        $this->assertEquals(13, $list->size());

        // Remove specific elements
        $this->assertTrue($list->remove(25));
        $this->assertTrue($list->remove(35));
        $this->assertEquals(11, $list->size());
        $this->assertFalse($list->contains(25));
        $this->assertFalse($list->contains(35));

        // Remove non-existent element
        $this->assertFalse($list->remove(100));
        $this->assertEquals(11, $list->size());

        // Remove duplicate (should remove only one)
        $this->assertTrue($list->remove(20));
        $this->assertEquals(10, $list->size());
        $this->assertTrue($list->contains(20), "Should still contain one instance of 20");

        // Add more elements
        for ($i = 60; $i <= 100; $i += 10) {
            $list->add($i);
        }
        $this->assertEquals(15, $list->size());

        // Clear and verify
        $list->clear();
        $this->assertEquals(0, $list->size());
        $this->assertFalse($list->contains(50));
    }

    public function testCompleteWorkflowString(): void
    {
        $list = new StringSortedLinkedList();

        // Add mixed case strings
        $words = ['apple', 'Banana', 'cherry', 'Date', 'elderberry', 'Fig', 'grape'];
        foreach ($words as $word) {
            $list->add($word);
        }

        $this->assertEquals(7, $list->size());

        // Verify case-sensitive sorting
        $this->assertTrue($list->contains('Banana'));
        $this->assertTrue($list->contains('apple'));
        $this->assertFalse($list->contains('banana')); // Different case

        // Add duplicates with different cases
        $list->add('apple');
        $list->add('Apple');
        $list->add('APPLE');
        $this->assertEquals(10, $list->size());

        // Remove operations
        $this->assertTrue($list->remove('apple'));
        $this->assertEquals(9, $list->size());
        $this->assertTrue($list->contains('apple'), "Should still have one 'apple'");

        // Add special characters
        $list->add('zebra!');
        $list->add('123abc');
        $list->add('@special');
        $this->assertEquals(12, $list->size());

        // Complex removal sequence
        $removeSequence = ['Fig', 'grape', '123abc'];
        foreach ($removeSequence as $item) {
            $this->assertTrue($list->remove($item));
        }
        $this->assertEquals(9, $list->size());

        // Clear
        $list->clear();
        $this->assertEquals(0, $list->size());
    }

    public function testCompleteWorkflowFloat(): void
    {
        $list = new FloatSortedLinkedList();

        // Add various float values
        $values = [3.14, 2.71, 1.41, 0.5, -1.5, -0.25, 10.01, 9.99];
        foreach ($values as $value) {
            $list->add($value);
        }

        $this->assertEquals(8, $list->size());

        // Verify negative values are handled correctly
        $this->assertTrue($list->contains(-1.5));
        $this->assertTrue($list->contains(-0.25));

        // Add very close values
        $list->add(3.14159);
        $list->add(3.141);
        $this->assertEquals(10, $list->size());

        // Remove operations with precision
        $this->assertTrue($list->remove(3.14));
        $this->assertEquals(9, $list->size());
        $this->assertTrue($list->contains(3.14159));
        $this->assertTrue($list->contains(3.141));

        // Add zero and near-zero values
        $list->add(0.0);
        $list->add(0.00001);
        $list->add(-0.00001);
        $this->assertEquals(12, $list->size());

        // Test removal of negative values
        $this->assertTrue($list->remove(-1.5));
        $this->assertTrue($list->remove(-0.25));
        $this->assertEquals(10, $list->size());

        // Clear
        $list->clear();
        $this->assertEquals(0, $list->size());
    }

    public function testMixedOperationSequence(): void
    {
        $list = new IntegerSortedLinkedList();

        // Phase 1: Build initial list
        for ($i = 1; $i <= 20; $i++) {
            $list->add($i * 5);
        }
        $this->assertEquals(20, $list->size());

        // Phase 2: Remove every third element
        for ($i = 3; $i <= 20; $i += 3) {
            $list->remove($i * 5);
        }
        $this->assertEquals(14, $list->size());

        // Phase 3: Add elements in gaps
        for ($i = 1; $i <= 20; $i++) {
            $list->add($i * 5 + 2);
        }
        $this->assertEquals(34, $list->size());

        // Phase 4: Check specific elements
        $this->assertTrue($list->contains(5));   // Original
        $this->assertTrue($list->contains(7));   // Added in phase 3
        $this->assertFalse($list->contains(15)); // Removed in phase 2
        $this->assertTrue($list->contains(17));  // Added in phase 3

        // Phase 5: Mass removal
        $removeCount = 0;
        for ($i = 0; $i <= 102; $i += 2) {
            if ($list->remove($i)) {
                $removeCount++;
            }
        }
        $this->assertGreaterThan(10, $removeCount);
        $this->assertLessThan(34, $list->size());

        // Phase 6: Final clear
        $list->clear();
        $this->assertEquals(0, $list->size());
    }

    public function testAlternatingAddRemove(): void
    {
        $list = new IntegerSortedLinkedList();

        // Alternating pattern
        for ($i = 0; $i < 50; $i++) {
            // Add two elements
            $list->add($i * 2);
            $list->add($i * 2 + 1);

            // Remove one element
            if ($i > 0) {
                $list->remove($i - 1);
            }
        }

        // Should have grown despite removals
        $this->assertGreaterThan(50, $list->size());
        $this->assertLessThan(100, $list->size());

        // Verify structure integrity
        $this->assertTrue($list->contains(98));
        $this->assertTrue($list->contains(99));
        $this->assertFalse($list->contains(-1));
    }

    public function testBatchOperations(): void
    {
        $list = new IntegerSortedLinkedList();

        // Batch add
        $batch1 = range(1, 100, 2); // Odd numbers 1-99
        foreach ($batch1 as $value) {
            $list->add($value);
        }
        $this->assertEquals(50, $list->size());

        // Batch add even numbers
        $batch2 = range(2, 100, 2); // Even numbers 2-100
        foreach ($batch2 as $value) {
            $list->add($value);
        }
        $this->assertEquals(100, $list->size());

        // Batch remove multiples of 3
        $removeCount = 0;
        for ($i = 3; $i <= 100; $i += 3) {
            if ($list->remove($i)) {
                $removeCount++;
            }
        }
        $this->assertEquals(33, $removeCount);
        $this->assertEquals(67, $list->size());

        // Batch check for multiples of 5
        $containsCount = 0;
        for ($i = 5; $i <= 100; $i += 5) {
            if ($list->contains($i)) {
                $containsCount++;
            }
        }
        // Should have some multiples of 5 that aren't multiples of 3
        $this->assertGreaterThan(10, $containsCount);
    }

    public function testStateConsistencyAfterErrors(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add initial elements
        for ($i = 1; $i <= 10; $i++) {
            $list->add($i);
        }
        $this->assertEquals(10, $list->size());

        // Try to remove non-existent elements
        $this->assertFalse($list->remove(0));
        $this->assertFalse($list->remove(11));
        $this->assertFalse($list->remove(100));

        // Size should remain unchanged
        $this->assertEquals(10, $list->size());

        // Original elements should still be there
        for ($i = 1; $i <= 10; $i++) {
            $this->assertTrue($list->contains($i));
        }

        // Continue with normal operations
        $list->add(11);
        $list->add(12);
        $this->assertTrue($list->remove(6));
        $this->assertEquals(11, $list->size());

        // Clear and verify empty state
        $list->clear();
        $this->assertEquals(0, $list->size());
        $this->assertFalse($list->remove(1));
        $this->assertFalse($list->contains(1));

        // Can still add after clear
        $list->add(100);
        $this->assertEquals(1, $list->size());
        $this->assertTrue($list->contains(100));
    }

    public function testLongRunningSequence(): void
    {
        $list = new StringSortedLinkedList();

        // Simulate long-running application with various operations
        $operations = [
            ['add', 'start'],
            ['add', 'middle'],
            ['add', 'end'],
            ['contains', 'middle'],
            ['remove', 'middle'],
            ['add', 'another'],
            ['add', 'test'],
            ['size', null],
            ['add', 'zebra'],
            ['remove', 'start'],
            ['add', 'alpha'],
            ['add', 'beta'],
            ['contains', 'end'],
            ['clear', null],
            ['add', 'fresh'],
            ['add', 'start'],
            ['size', null],
        ];

        $sizeHistory = [];

        foreach ($operations as $op) {
            [$operation, $value] = $op;

            switch ($operation) {
                case 'add':
                    $list->add($value);
                    break;
                case 'remove':
                    $list->remove($value);
                    break;
                case 'contains':
                    $list->contains($value);
                    break;
                case 'clear':
                    $list->clear();
                    break;
                case 'size':
                    $sizeHistory[] = $list->size();
                    break;
            }
        }

        // Verify final state
        $this->assertEquals(2, $list->size());
        $this->assertTrue($list->contains('fresh'));
        $this->assertTrue($list->contains('start'));
        $this->assertEquals([4, 2], $sizeHistory);
    }
}
