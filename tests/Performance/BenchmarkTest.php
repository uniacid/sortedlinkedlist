<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Performance;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class BenchmarkTest extends TestCase
{
    private function measureTime(callable $operation): float
    {
        $start = microtime(true);
        $operation();
        return microtime(true) - $start;
    }

    private function generateRandomIntegers(int $count): array
    {
        $numbers = [];
        for ($i = 0; $i < $count; $i++) {
            $numbers[] = random_int(1, 10000);
        }
        return $numbers;
    }

    private function generateRandomStrings(int $count): array
    {
        $strings = [];
        for ($i = 0; $i < $count; $i++) {
            $strings[] = bin2hex(random_bytes(10));
        }
        return $strings;
    }

    private function generateRandomFloats(int $count): array
    {
        $floats = [];
        for ($i = 0; $i < $count; $i++) {
            $floats[] = random_int(1, 10000) / 100.0;
        }
        return $floats;
    }

    public function testIntegerListInsertionPerformance(): void
    {
        $sizes = [100, 500, 1000, 2000];
        $results = [];

        foreach ($sizes as $size) {
            $data = $this->generateRandomIntegers($size);
            $list = new IntegerSortedLinkedList();

            $time = $this->measureTime(function() use ($list, $data) {
                foreach ($data as $value) {
                    $list->add($value);
                }
            });

            $results[$size] = $time;
            $this->assertLessThan($size * 0.005, $time, "Insertion of $size integers took too long");
        }

        // Verify O(n) complexity trend (allow for O(n^2) worst case in sorted insertion)
        $ratio = $results[2000] / $results[1000];
        $this->assertLessThan(5.0, $ratio, "Performance does not scale linearly");
    }

    public function testStringListInsertionPerformance(): void
    {
        $sizes = [100, 500, 1000, 2000];
        $results = [];

        foreach ($sizes as $size) {
            $data = $this->generateRandomStrings($size);
            $list = new StringSortedLinkedList();

            $time = $this->measureTime(function() use ($list, $data) {
                foreach ($data as $value) {
                    $list->add($value);
                }
            });

            $results[$size] = $time;
            $this->assertLessThan($size * 0.005, $time, "Insertion of $size strings took too long");
        }

        // Verify O(n) complexity trend (allow for O(n^2) worst case in sorted insertion)
        $ratio = $results[2000] / $results[1000];
        $this->assertLessThan(5.0, $ratio, "Performance does not scale linearly");
    }

    public function testFloatListInsertionPerformance(): void
    {
        $sizes = [100, 500, 1000, 2000];
        $results = [];

        foreach ($sizes as $size) {
            $data = $this->generateRandomFloats($size);
            $list = new FloatSortedLinkedList();

            $time = $this->measureTime(function() use ($list, $data) {
                foreach ($data as $value) {
                    $list->add($value);
                }
            });

            $results[$size] = $time;
            $this->assertLessThan($size * 0.005, $time, "Insertion of $size floats took too long");
        }

        // Verify O(n) complexity trend (allow for O(n^2) worst case in sorted insertion)
        $ratio = $results[2000] / $results[1000];
        $this->assertLessThan(5.0, $ratio, "Performance does not scale linearly");
    }

    public function testSearchPerformance(): void
    {
        $list = new IntegerSortedLinkedList();
        $size = 1000;

        // Add elements
        for ($i = 0; $i < $size; $i++) {
            $list->add($i);
        }

        // Best case: searching for first element
        $bestTime = $this->measureTime(function() use ($list) {
            for ($i = 0; $i < 100; $i++) {
                $list->contains(0);
            }
        });

        // Worst case: searching for last element
        $worstTime = $this->measureTime(function() use ($list) {
            for ($i = 0; $i < 100; $i++) {
                $list->contains(999);
            }
        });

        // Worst case should be significantly slower than best case (O(n) vs O(1))
        $this->assertGreaterThan(5, $worstTime / $bestTime, "Search performance does not show O(n) behavior");
    }

    public function testRemovalPerformance(): void
    {
        $sizes = [100, 500, 1000];
        $results = [];

        foreach ($sizes as $size) {
            $list = new IntegerSortedLinkedList();

            // Add elements
            for ($i = 0; $i < $size; $i++) {
                $list->add($i);
            }

            // Remove half the elements
            $time = $this->measureTime(function() use ($list, $size) {
                for ($i = 0; $i < $size / 2; $i++) {
                    $list->remove($i * 2);
                }
            });

            $results[$size] = $time;
            $this->assertLessThan($size * 0.005, $time, "Removal of $size/2 elements took too long");
        }

        // Verify O(n) complexity trend (allow for O(n^2) worst case)
        $ratio = $results[1000] / $results[500];
        $this->assertLessThan(5.0, $ratio, "Removal performance does not scale linearly");
    }

    public function testSizePerformance(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add many elements
        for ($i = 0; $i < 10000; $i++) {
            $list->add($i);
        }

        // Size() should be O(1)
        $time = $this->measureTime(function() use ($list) {
            for ($i = 0; $i < 10000; $i++) {
                $list->size();
            }
        });

        $this->assertLessThan(0.05, $time, "Size operation should be O(1) constant time");
    }

    public function testClearPerformance(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add many elements
        for ($i = 0; $i < 10000; $i++) {
            $list->add($i);
        }

        // Clear should be O(1)
        $time = $this->measureTime(function() use ($list) {
            $list->clear();
        });

        $this->assertLessThan(0.005, $time, "Clear operation should be O(1) constant time");
        $this->assertEquals(0, $list->size());
    }
}