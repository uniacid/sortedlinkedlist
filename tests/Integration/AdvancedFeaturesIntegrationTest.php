<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests\Integration;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\Comparator\NumericComparator;
use SortedLinkedList\Comparator\StringComparator;
use SortedLinkedList\Comparator\DateComparator;
use SortedLinkedList\Comparator\CallableComparator;
use SortedLinkedList\Comparator\ReverseComparator;

/**
 * Integration tests combining all advanced features
 */
class AdvancedFeaturesIntegrationTest extends TestCase
{
    /**
     * Test combining iterator with binary search for efficient range queries
     */
    public function testIteratorWithBinarySearchRangeQuery(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add test data
        for ($i = 0; $i < 1000; $i += 10) {
            $list->add($i);
        }

        // Find range [250, 750] using binary search for start position
        $startIndex = $list->binarySearch(250);
        $endIndex = $list->binarySearch(750);

        // Use array access to get values at those positions
        $this->assertEquals(250, $list[$startIndex]);
        $this->assertEquals(750, $list[$endIndex]);

        // Iterate through range using iterator
        $rangeValues = [];
        foreach ($list as $key => $value) {
            if ($key >= $startIndex && $key <= $endIndex) {
                $rangeValues[] = $value;
            }
            if ($key > $endIndex) {
                break;
            }
        }

        $this->assertCount(51, $rangeValues); // 250, 260, ..., 750
        $this->assertEquals(250, $rangeValues[0]);
        $this->assertEquals(750, $rangeValues[50]);
    }

    /**
     * Test custom comparators with bulk operations
     */
    public function testCustomComparatorsWithBulkOperations(): void
    {
        // Create list with reverse numeric comparator
        $reverseComparator = new ReverseComparator(new NumericComparator());
        $list = new ImmutableSortedLinkedList($reverseComparator);

        // Bulk add data
        $data = [5, 2, 8, 1, 9, 3, 7, 4, 6];
        $list = $list->withAddAll($data);

        // Verify reverse order
        $array = $list->toArray();
        $this->assertEquals([9, 8, 7, 6, 5, 4, 3, 2, 1], $array);

        // Filter even numbers
        $evenList = $list->filter(function ($value) {
            return $value % 2 === 0;
        });

        $this->assertEquals([8, 6, 4, 2], $evenList->toArray());

        // Map to double values
        $doubledList = $evenList->map(function ($value) {
            return $value * 2;
        });

        $this->assertEquals([16, 12, 8, 4], $doubledList->toArray());
    }

    /**
     * Test immutable operations with structural sharing
     */
    public function testImmutableStructuralSharing(): void
    {
        $comparator = new NumericComparator();
        $original = new ImmutableSortedLinkedList($comparator);

        // Build initial list
        for ($i = 0; $i < 100; $i++) {
            $original = $original->withAdd($i);
        }

        // Create multiple versions
        $version1 = $original->withAdd(1000);
        $version2 = $original->withAdd(2000);
        $version3 = $version1->withAdd(3000);

        // All versions should be independent
        $this->assertEquals(100, $original->size());
        $this->assertEquals(101, $version1->size());
        $this->assertEquals(101, $version2->size());
        $this->assertEquals(102, $version3->size());

        // Verify correct values
        $this->assertTrue($version1->contains(1000));
        $this->assertFalse($version1->contains(2000));
        $this->assertTrue($version2->contains(2000));
        $this->assertFalse($version2->contains(1000));
        $this->assertTrue($version3->contains(1000));
        $this->assertTrue($version3->contains(3000));
    }

    /**
     * Test complex data transformations with chained operations
     */
    public function testComplexDataTransformations(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add sample data
        for ($i = 1; $i <= 100; $i++) {
            $list->add($i);
        }

        // Complex transformation:
        // 1. Filter multiples of 3
        // 2. Map to squares
        // 3. Filter values > 1000
        // 4. Reduce to sum
        $result = $list
            ->filter(function ($value) {
                return $value % 3 === 0;
            })
            ->map(function ($value) {
                return $value * $value;
            })
            ->filter(function ($value) {
                return $value > 1000;
            })
            ->reduce(function ($carry, $value) {
                return $carry + $value;
            }, 0);

        // Values that are multiples of 3: 3, 6, 9, ..., 99
        // After squaring: 9, 36, 81, ..., 9801
        // Filter > 1000: 33^2=1089, 36^2=1296, ..., 99^2=9801
        // Sum of squares from 33^2 to 99^2 where original % 3 == 0
        $this->assertGreaterThan(0, $result);
        $this->assertEquals(109296, $result); // Corrected sum
    }

    /**
     * Test bidirectional iteration with custom comparators
     */
    public function testBidirectionalIterationWithComparators(): void
    {
        $stringList = new StringSortedLinkedList();
        $strings = ['alpha', 'beta', 'gamma', 'delta', 'epsilon'];

        foreach ($strings as $string) {
            $stringList->add($string);
        }

        // Forward iteration
        $forward = [];
        foreach ($stringList as $value) {
            $forward[] = $value;
        }
        // Strings are sorted alphabetically
        $sortedStrings = ['alpha', 'beta', 'delta', 'epsilon', 'gamma'];
        $this->assertEquals($sortedStrings, $forward);

        // Backward iteration
        $backward = [];
        $stringList->end();
        $backward[] = $stringList->current();

        // Use key() to track position to avoid infinite loop
        while ($stringList->key() > 0) {
            $stringList->prev();
            $backward[] = $stringList->current();
        }

        $this->assertEquals(array_reverse($sortedStrings), $backward);

        // Reset for next use
        $stringList->rewind();
        $this->assertEquals('alpha', $stringList->current());
    }

    /**
     * Test performance tracking with operations
     */
    public function testPerformanceTracking(): void
    {
        $list = new IntegerSortedLinkedList();

        // Perform various operations
        for ($i = 0; $i < 100; $i++) {
            $list->add($i);
        }

        // Binary searches
        for ($i = 0; $i < 50; $i += 10) {
            $list->binarySearch($i);
        }

        // Linear searches
        for ($i = 5; $i < 50; $i += 10) {
            $list->contains($i);
        }

        // Just verify operations worked
        $this->assertEquals(100, $list->size());
        $this->assertTrue($list->contains(50));
        $this->assertNotFalse($list->binarySearch(50));
    }

    /**
     * Test array access with bulk operations
     */
    public function testArrayAccessWithBulkOperations(): void
    {
        $list = new IntegerSortedLinkedList();

        // Bulk add
        $list->addAll(range(0, 99));

        // Access via array syntax
        $this->assertEquals(50, $list[50]);
        $this->assertEquals(0, $list[0]);
        $this->assertEquals(99, $list[99]);

        // Modify via array syntax (adds if doesn't exist at index)
        $list[100] = 1000;
        $this->assertTrue($list->contains(1000));

        // Check bounds
        $this->assertTrue(isset($list[50]));
        $this->assertFalse(isset($list[200]));

        // Remove via unset
        unset($list[50]);
        $this->assertFalse($list->contains(50));
    }

    /**
     * Test date comparator with real-world scenario
     */
    public function testDateComparatorRealWorld(): void
    {
        $dateComparator = new DateComparator();
        $list = new ImmutableSortedLinkedList($dateComparator);

        $dates = [
            new \DateTime('2024-01-15'),
            new \DateTime('2023-12-25'),
            new \DateTime('2024-03-01'),
            new \DateTime('2023-11-30'),
            new \DateTime('2024-02-14')
        ];

        $list = $list->withAddAll($dates);

        $sorted = $list->toArray();
        $expected = [
            new \DateTime('2023-11-30'),
            new \DateTime('2023-12-25'),
            new \DateTime('2024-01-15'),
            new \DateTime('2024-02-14'),
            new \DateTime('2024-03-01')
        ];

        // Compare timestamps since DateTime objects won't be equal
        $sortedTimestamps = array_map(fn($d) => $d->getTimestamp(), $sorted);
        $expectedTimestamps = array_map(fn($d) => $d->getTimestamp(), $expected);
        $this->assertEquals($expectedTimestamps, $sortedTimestamps);

        // Filter dates in 2024
        $dates2024 = $list->filter(function ($date) {
            return $date->format('Y') === '2024';
        });

        $this->assertCount(3, $dates2024->toArray());
    }

    /**
     * Test CallableComparator with complex objects
     */
    public function testCallableComparatorComplexScenario(): void
    {
        // Create sample objects
        $users = [
            (object)['id' => 3, 'name' => 'Charlie', 'score' => 85],
            (object)['id' => 1, 'name' => 'Alice', 'score' => 92],
            (object)['id' => 2, 'name' => 'Bob', 'score' => 78],
            (object)['id' => 4, 'name' => 'David', 'score' => 92],
        ];

        // Sort by score, then by name using CallableComparator
        $comparator = new CallableComparator(function ($a, $b) {
            $scoreCmp = $a->score <=> $b->score;
            if ($scoreCmp !== 0) {
                return $scoreCmp;
            }
            return strcmp($a->name, $b->name);
        });

        $list = new ImmutableSortedLinkedList($comparator);
        $list = $list->withAddAll($users);

        $sorted = $list->toArray();

        // Bob (78), Charlie (85), Alice (92), David (92)
        $this->assertEquals('Bob', $sorted[0]->name);
        $this->assertEquals('Charlie', $sorted[1]->name);
        $this->assertEquals('Alice', $sorted[2]->name); // Alice before David (same score)
        $this->assertEquals('David', $sorted[3]->name);
    }

    /**
     * Test combination of all features in a complex scenario
     */
    public function testCompleteFeatureIntegration(): void
    {
        // Create immutable list with custom comparator
        $comparator = new ReverseComparator(new NumericComparator());
        $list = new ImmutableSortedLinkedList($comparator);

        // Bulk add initial data
        $initialData = range(1, 50);
        $list = $list->withAddAll($initialData);

        // Create multiple versions with different operations
        $version1 = $list->filter(function ($value) {
            return $value % 2 === 0;
        }); // Even numbers only

        $version2 = $list->map(function ($value) {
            return $value * 3;
        }); // Triple all values

        $version3 = $version1->withAddAll([100, 200, 300]); // Add more to evens

        // Use binary search on different versions
        $index1 = $version1->binarySearch(20);
        $this->assertNotFalse($index1);

        $index2 = $version2->binarySearch(30); // 10 * 3
        $this->assertNotFalse($index2);

        // Iterate with array access
        $sum = 0;
        for ($i = 0; $i < min(10, $version3->size()); $i++) {
            if (isset($version3[$i])) {
                $sum += $version3[$i];
            }
        }
        $this->assertGreaterThan(0, $sum);

        // Complex reduction
        $product = $version1->reduce(function (int $carry, int $value): int {
            if ($value <= 10) {
                return $carry * $value;
            }
            return $carry;
        }, 1);

        $this->assertEquals(3840, $product); // 2 * 4 * 6 * 8 * 10

        // Verify immutability
        $this->assertEquals(50, $list->size());
        $this->assertEquals(25, $version1->size());
        $this->assertEquals(50, $version2->size());
        $this->assertEquals(28, $version3->size());

        // Performance verification
        for ($i = 0; $i < 20; $i++) {
            $list->contains($i * 2);
        }
        // Just verify list is still functional
        $this->assertEquals(50, $list->size());
    }

    /**
     * Test memory efficiency with large datasets
     */
    public function testMemoryEfficiencyLargeDataset(): void
    {
        $list = new IntegerSortedLinkedList();
        $memoryBefore = memory_get_usage();

        // Add large dataset
        for ($i = 0; $i < 10000; $i++) {
            $list->add($i);
        }

        $memoryAfter = memory_get_usage();
        $memoryUsed = $memoryAfter - $memoryBefore;

        // Memory usage should be reasonable (less than 10MB for 10k integers)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed);

        // Test operations still work efficiently
        $found = $list->binarySearch(5000);
        $this->assertNotFalse($found);

        // Bulk operations
        $filtered = $list->filter(function ($value) {
            return $value >= 5000 && $value < 6000;
        });
        $this->assertEquals(1000, $filtered->size());
    }
}
