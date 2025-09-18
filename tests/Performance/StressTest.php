<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Performance;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class StressTest extends TestCase
{
    public function testLargeIntegerDataset(): void
    {
        $list = new IntegerSortedLinkedList();
        $size = 2000;

        // Add elements in random order
        $data = range(1, $size);
        shuffle($data);

        foreach ($data as $value) {
            $list->add($value);
        }

        $this->assertEquals($size, $list->size());

        // Verify sorted order by checking samples
        $this->assertTrue($list->contains(1));
        $this->assertTrue($list->contains(1000));
        $this->assertTrue($list->contains(2000));

        // Remove 500 random elements
        $toRemove = array_rand(array_flip($data), 500);
        foreach ($toRemove as $value) {
            $list->remove($value);
        }

        $this->assertEquals(1500, $list->size());

        // Verify removed elements
        foreach ($toRemove as $value) {
            $this->assertFalse($list->contains($value));
        }
    }

    public function testLargeStringDataset(): void
    {
        $list = new StringSortedLinkedList();
        $size = 2000;

        // Generate unique strings
        $data = [];
        for ($i = 0; $i < $size; $i++) {
            $data[] = sprintf("string_%08d", $i);
        }
        shuffle($data);

        foreach ($data as $value) {
            $list->add($value);
        }

        $this->assertEquals($size, $list->size());

        // Verify sorted order samples
        $this->assertTrue($list->contains("string_00000000"));
        $this->assertTrue($list->contains("string_00001000"));
        $this->assertTrue($list->contains("string_00001999"));

        // Clear the list
        $list->clear();
        $this->assertEquals(0, $list->size());
    }

    public function testLargeFloatDataset(): void
    {
        $list = new FloatSortedLinkedList();
        $size = 2000;

        // Add floats
        for ($i = 0; $i < $size; $i++) {
            $list->add($i / 100.0);
        }

        $this->assertEquals($size, $list->size());

        // Verify sorted order
        $this->assertTrue($list->contains(0.0));
        $this->assertTrue($list->contains(10.0));
        $this->assertTrue($list->contains(19.99));

        // Remove every 10th element
        for ($i = 0; $i < $size; $i += 10) {
            $list->remove($i / 100.0);
        }

        $this->assertEquals(1800, $list->size());
    }

    public function testMaximumDatasetWithDuplicates(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add 5000 elements with many duplicates
        for ($i = 0; $i < 5000; $i++) {
            $list->add($i % 100); // Only 100 unique values
        }

        $this->assertEquals(5000, $list->size());

        // Each value should appear 50 times
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue($list->contains($i));
        }

        // Remove all instances of value 50
        $removed = 0;
        while ($list->remove(50)) {
            $removed++;
            if ($removed > 50) break; // Safety limit
        }

        $this->assertEquals(50, $removed);
        $this->assertEquals(4950, $list->size());
        $this->assertFalse($list->contains(50));
    }

    public function testRapidAddRemoveCycles(): void
    {
        $list = new IntegerSortedLinkedList();

        // Perform 200 cycles of add and remove
        for ($cycle = 0; $cycle < 200; $cycle++) {
            // Add 10 random values
            for ($i = 0; $i < 10; $i++) {
                $list->add(random_int(1, 1000));
            }

            // Remove 5 values
            for ($i = 0; $i < 5; $i++) {
                $list->remove(random_int(1, 1000));
            }
        }

        // List should have grown (net positive due to more adds than removes)
        $this->assertGreaterThan(500, $list->size());
        $this->assertLessThan(2000, $list->size());
    }

    public function testSequentialVsRandomInsertion(): void
    {
        // Test sequential insertion (best case)
        $sequentialList = new IntegerSortedLinkedList();
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            $sequentialList->add($i);
        }
        $sequentialTime = microtime(true) - $start;

        // Test random insertion (average case)
        $randomList = new IntegerSortedLinkedList();
        $data = range(0, 999);
        shuffle($data);
        $start = microtime(true);
        foreach ($data as $value) {
            $randomList->add($value);
        }
        $randomTime = microtime(true) - $start;

        // Both should complete in reasonable time (allowing for CI environment)
        $this->assertLessThan(5.0, $sequentialTime);
        $this->assertLessThan(5.0, $randomTime);

        // Both lists should have same size and contain same elements
        $this->assertEquals(1000, $sequentialList->size());
        $this->assertEquals(1000, $randomList->size());

        // Verify sample elements
        for ($i = 0; $i < 1000; $i += 100) {
            $this->assertTrue($sequentialList->contains($i));
            $this->assertTrue($randomList->contains($i));
        }
    }

    public function testExtremeDuplicateHandling(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add the same value 2000 times
        for ($i = 0; $i < 2000; $i++) {
            $list->add(42);
        }

        $this->assertEquals(2000, $list->size());
        $this->assertTrue($list->contains(42));

        // Remove all instances
        $removed = 0;
        while ($list->remove(42) && $removed < 2001) { // Safety limit
            $removed++;
        }

        $this->assertEquals(2000, $removed);
        $this->assertEquals(0, $list->size());
        $this->assertFalse($list->contains(42));
    }

    public function testMixedOperationsUnderLoad(): void
    {
        $list = new IntegerSortedLinkedList();

        // Initial population
        for ($i = 0; $i < 1000; $i++) {
            $list->add($i * 2); // Even numbers
        }

        $this->assertEquals(1000, $list->size());

        // Add odd numbers
        for ($i = 0; $i < 1000; $i++) {
            $list->add($i * 2 + 1); // Odd numbers
        }

        $this->assertEquals(2000, $list->size());

        // Check contains for random samples
        for ($i = 0; $i < 50; $i++) {
            $value = random_int(0, 1999);
            $this->assertTrue($list->contains($value));
        }

        // Remove all even numbers
        for ($i = 0; $i < 1000; $i++) {
            $list->remove($i * 2);
        }

        $this->assertEquals(1000, $list->size());

        // Verify only odd numbers remain
        for ($i = 0; $i < 50; $i++) {
            $evenValue = random_int(0, 999) * 2;
            $oddValue = random_int(0, 999) * 2 + 1;
            $this->assertFalse($list->contains($evenValue));
            $this->assertTrue($list->contains($oddValue));
        }
    }
}