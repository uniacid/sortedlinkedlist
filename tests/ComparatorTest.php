<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\Comparator\ComparatorInterface;
use SortedLinkedList\Comparator\NumericComparator;
use SortedLinkedList\Comparator\StringComparator;
use SortedLinkedList\Comparator\DateComparator;
use SortedLinkedList\Comparator\CallableComparator;
use SortedLinkedList\Comparator\ReverseComparator;
use SortedLinkedList\Comparator\ComparatorFactory;
use SortedLinkedList\SortedLinkedList;

class ComparatorTest extends TestCase
{
    public function testComparatorInterfaceContract(): void
    {
        $comparator = new class implements ComparatorInterface {
            public function compare(mixed $a, mixed $b): int
            {
                return $a <=> $b;
            }
        };

        $this->assertInstanceOf(ComparatorInterface::class, $comparator);
        $this->assertEquals(0, $comparator->compare(5, 5));
        $this->assertLessThan(0, $comparator->compare(3, 7));
        $this->assertGreaterThan(0, $comparator->compare(10, 2));
    }

    public function testNumericComparatorWithIntegers(): void
    {
        $comparator = new NumericComparator();

        $this->assertEquals(0, $comparator->compare(5, 5));
        $this->assertLessThan(0, $comparator->compare(3, 7));
        $this->assertGreaterThan(0, $comparator->compare(10, 2));
        $this->assertLessThan(0, $comparator->compare(-5, 0));
        $this->assertGreaterThan(0, $comparator->compare(0, -5));
    }

    public function testNumericComparatorWithFloats(): void
    {
        $comparator = new NumericComparator();

        $this->assertEquals(0, $comparator->compare(3.14, 3.14));
        $this->assertLessThan(0, $comparator->compare(2.5, 3.7));
        $this->assertGreaterThan(0, $comparator->compare(10.5, 2.1));
        $this->assertLessThan(0, $comparator->compare(-5.5, 0.0));
    }

    public function testNumericComparatorWithMixedTypes(): void
    {
        $comparator = new NumericComparator();

        $this->assertEquals(0, $comparator->compare(5, 5.0));
        $this->assertLessThan(0, $comparator->compare(3, 7.5));
        $this->assertGreaterThan(0, $comparator->compare(10.5, 2));
    }

    public function testStringComparatorCaseSensitive(): void
    {
        $comparator = new StringComparator(true); // case-sensitive

        $this->assertEquals(0, $comparator->compare('hello', 'hello'));
        $this->assertLessThan(0, $comparator->compare('apple', 'banana'));
        $this->assertGreaterThan(0, $comparator->compare('zebra', 'ant'));

        // Case-sensitive: uppercase comes before lowercase in ASCII
        $this->assertLessThan(0, $comparator->compare('Apple', 'apple'));
        $this->assertGreaterThan(0, $comparator->compare('hello', 'Hello'));
    }

    public function testStringComparatorCaseInsensitive(): void
    {
        $comparator = new StringComparator(false); // case-insensitive

        $this->assertEquals(0, $comparator->compare('hello', 'HELLO'));
        $this->assertEquals(0, $comparator->compare('Apple', 'apple'));
        $this->assertLessThan(0, $comparator->compare('apple', 'BANANA'));
        $this->assertGreaterThan(0, $comparator->compare('ZEBRA', 'ant'));
    }

    public function testStringComparatorDefaultIsCaseSensitive(): void
    {
        $comparator = new StringComparator(); // default should be case-sensitive

        $this->assertNotEquals(0, $comparator->compare('hello', 'Hello'));
        $this->assertLessThan(0, $comparator->compare('Hello', 'hello'));
    }

    public function testDateComparator(): void
    {
        $comparator = new DateComparator();

        $date1 = new \DateTime('2024-01-01');
        $date2 = new \DateTime('2024-01-01');
        $date3 = new \DateTime('2024-06-15');
        $date4 = new \DateTime('2023-12-31');

        $this->assertEquals(0, $comparator->compare($date1, $date2));
        $this->assertLessThan(0, $comparator->compare($date1, $date3));
        $this->assertGreaterThan(0, $comparator->compare($date1, $date4));

        // Test with DateTimeImmutable
        $immutable1 = new \DateTimeImmutable('2024-03-15 10:30:00');
        $immutable2 = new \DateTimeImmutable('2024-03-15 10:30:00');
        $immutable3 = new \DateTimeImmutable('2024-03-15 15:45:00');

        $this->assertEquals(0, $comparator->compare($immutable1, $immutable2));
        $this->assertLessThan(0, $comparator->compare($immutable1, $immutable3));

        // Test mixed DateTime and DateTimeImmutable
        $this->assertLessThan(0, $comparator->compare($date1, $immutable1));
    }

    public function testCallableComparator(): void
    {
        // Test with closure
        $comparator = new CallableComparator(function($a, $b) {
            return strlen($a) <=> strlen($b);
        });

        $this->assertEquals(0, $comparator->compare('cat', 'dog'));
        $this->assertLessThan(0, $comparator->compare('hi', 'hello'));
        $this->assertGreaterThan(0, $comparator->compare('elephant', 'ant'));

        // Test with built-in function
        $comparator2 = new CallableComparator('strcmp');

        $this->assertEquals(0, $comparator2->compare('test', 'test'));
        $this->assertLessThan(0, $comparator2->compare('apple', 'banana'));
        $this->assertGreaterThan(0, $comparator2->compare('zebra', 'ant'));
    }

    public function testReverseComparator(): void
    {
        $baseComparator = new NumericComparator();
        $reverseComparator = new ReverseComparator($baseComparator);

        // Original comparator: 3 < 7, so returns negative
        // Reverse comparator: should return positive
        $this->assertGreaterThan(0, $reverseComparator->compare(3, 7));
        $this->assertLessThan(0, $reverseComparator->compare(10, 2));
        $this->assertEquals(0, $reverseComparator->compare(5, 5));

        // Test with string comparator
        $stringComparator = new StringComparator();
        $reverseStringComparator = new ReverseComparator($stringComparator);

        $this->assertGreaterThan(0, $reverseStringComparator->compare('apple', 'banana'));
        $this->assertLessThan(0, $reverseStringComparator->compare('zebra', 'ant'));
    }

    public function testObjectComparisonWithCustomLogic(): void
    {
        // Create a custom comparator for objects
        $personComparator = new CallableComparator(function($a, $b) {
            // Sort by age first, then by name
            $ageComparison = $a->age <=> $b->age;
            if ($ageComparison !== 0) {
                return $ageComparison;
            }
            return strcmp($a->name, $b->name);
        });

        $person1 = (object)['name' => 'Alice', 'age' => 30];
        $person2 = (object)['name' => 'Bob', 'age' => 25];
        $person3 = (object)['name' => 'Charlie', 'age' => 30];
        $person4 = (object)['name' => 'Alice', 'age' => 30]; // Same as person1

        $this->assertLessThan(0, $personComparator->compare($person2, $person1)); // Bob (25) < Alice (30)
        $this->assertLessThan(0, $personComparator->compare($person1, $person3)); // Alice (30) < Charlie (30)
        $this->assertEquals(0, $personComparator->compare($person1, $person4)); // Alice (30) == Alice (30)
    }

    public function testComparatorChaining(): void
    {
        // Create a comparator that sorts by multiple fields
        // First by department, then by salary (reversed), then by name

        $employees = [
            (object)['name' => 'Alice', 'department' => 'Engineering', 'salary' => 100000],
            (object)['name' => 'Bob', 'department' => 'Sales', 'salary' => 80000],
            (object)['name' => 'Charlie', 'department' => 'Engineering', 'salary' => 120000],
            (object)['name' => 'David', 'department' => 'Engineering', 'salary' => 100000],
        ];

        $chainedComparator = new CallableComparator(function($a, $b) {
            // First compare by department
            $deptComparison = strcmp($a->department, $b->department);
            if ($deptComparison !== 0) {
                return $deptComparison;
            }

            // Then by salary (reversed for descending order)
            $salaryComparison = $b->salary <=> $a->salary;
            if ($salaryComparison !== 0) {
                return $salaryComparison;
            }

            // Finally by name
            return strcmp($a->name, $b->name);
        });

        // Engineering dept comes before Sales
        $this->assertLessThan(0, $chainedComparator->compare($employees[0], $employees[1]));

        // Within Engineering: Charlie (120k) comes before Alice (100k)
        $this->assertGreaterThan(0, $chainedComparator->compare($employees[0], $employees[2]));

        // Within Engineering, same salary: Alice comes before David
        $this->assertLessThan(0, $chainedComparator->compare($employees[0], $employees[3]));
    }

    public function testComparatorFactoryMethods(): void
    {
        // Test numeric factory
        $numeric = ComparatorFactory::numeric();
        $this->assertInstanceOf(NumericComparator::class, $numeric);
        $this->assertLessThan(0, $numeric->compare(3, 7));

        // Test string factory (case-sensitive)
        $string = ComparatorFactory::string();
        $this->assertInstanceOf(StringComparator::class, $string);
        $this->assertLessThan(0, $string->compare('apple', 'banana'));

        // Test string factory (case-insensitive)
        $stringInsensitive = ComparatorFactory::string(false);
        $this->assertInstanceOf(StringComparator::class, $stringInsensitive);
        $this->assertEquals(0, $stringInsensitive->compare('Apple', 'apple'));

        // Test date factory
        $date = ComparatorFactory::date();
        $this->assertInstanceOf(DateComparator::class, $date);
        $date1 = new \DateTime('2024-01-01');
        $date2 = new \DateTime('2024-06-15');
        $this->assertLessThan(0, $date->compare($date1, $date2));

        // Test reverse factory
        $reverseNumeric = ComparatorFactory::reverse(ComparatorFactory::numeric());
        $this->assertInstanceOf(ReverseComparator::class, $reverseNumeric);
        $this->assertGreaterThan(0, $reverseNumeric->compare(3, 7));

        // Test callable factory
        $callable = ComparatorFactory::callable(function($a, $b) {
            return $a <=> $b;
        });
        $this->assertInstanceOf(CallableComparator::class, $callable);
        $this->assertLessThan(0, $callable->compare(1, 2));
    }

    public function testSortedLinkedListWithCustomComparator(): void
    {
        // Create a custom list with reverse numeric comparator
        $reverseComparator = new ReverseComparator(new NumericComparator());

        $list = new class($reverseComparator) extends SortedLinkedList {
            private ComparatorInterface $customComparator;

            public function __construct(ComparatorInterface $comparator)
            {
                parent::__construct($comparator);
                $this->customComparator = $comparator;
            }

            protected function compare(mixed $a, mixed $b): int
            {
                return $this->customComparator->compare($a, $b);
            }
        };

        $list->add(5);
        $list->add(2);
        $list->add(8);
        $list->add(1);
        $list->add(9);

        // Should be sorted in descending order
        $expected = [9, 8, 5, 2, 1];
        $actual = [];
        foreach ($list as $value) {
            $actual[] = $value;
        }

        $this->assertEquals($expected, $actual);
    }

    public function testComparatorWithNullHandling(): void
    {
        // Create a comparator that handles null values
        $nullSafeComparator = new CallableComparator(function($a, $b) {
            if ($a === null && $b === null) {
                return 0;
            }
            if ($a === null) {
                return -1; // null comes first
            }
            if ($b === null) {
                return 1;
            }
            return $a <=> $b;
        });

        $this->assertEquals(0, $nullSafeComparator->compare(null, null));
        $this->assertLessThan(0, $nullSafeComparator->compare(null, 5));
        $this->assertGreaterThan(0, $nullSafeComparator->compare(5, null));
        $this->assertLessThan(0, $nullSafeComparator->compare(3, 7));
    }

    public function testComparatorTypeValidation(): void
    {
        $numericComparator = new NumericComparator();

        // Should throw exception for non-numeric types
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('NumericComparator can only compare numeric values');
        $numericComparator->compare('string', 'another');
    }

    public function testStringComparatorTypeValidation(): void
    {
        $stringComparator = new StringComparator();

        // Should throw exception for non-string types
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('StringComparator can only compare string values');
        $stringComparator->compare(123, 456);
    }

    public function testDateComparatorTypeValidation(): void
    {
        $dateComparator = new DateComparator();

        // Should throw exception for non-date types
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('DateComparator can only compare DateTime objects');
        $dateComparator->compare('2024-01-01', '2024-06-15');
    }
}