# Custom Comparators Documentation

The SortedLinkedList library now supports custom comparators, allowing flexible sorting logic beyond the default implementations.

## Table of Contents
- [Basic Usage](#basic-usage)
- [Built-in Comparators](#built-in-comparators)
- [Custom Comparators](#custom-comparators)
- [Advanced Examples](#advanced-examples)

## Basic Usage

### Using Default Sorting
```php
use SortedLinkedList\IntegerSortedLinkedList;

// Default ascending order for integers
$list = new IntegerSortedLinkedList();
$list->add(5);
$list->add(2);
$list->add(8);
// Result: [2, 5, 8]
```

### Using Custom Comparator
```php
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\Comparator\ComparatorFactory;

// Reverse order using factory method
$reverseList = new IntegerSortedLinkedList(
    ComparatorFactory::reverse(ComparatorFactory::numeric())
);
$reverseList->add(5);
$reverseList->add(2);
$reverseList->add(8);
// Result: [8, 5, 2]
```

## Built-in Comparators

### NumericComparator
Compares numeric values (integers and floats):

```php
use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\Comparator\NumericComparator;

$list = new class(new NumericComparator()) extends SortedLinkedList {
    // Custom list implementation
};
$list->add(3.14);
$list->add(2.5);
$list->add(10);
// Result: [2.5, 3.14, 10]
```

### StringComparator
Compares strings with optional case sensitivity:

```php
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\Comparator\StringComparator;

// Case-insensitive sorting
$list = new StringSortedLinkedList(new StringComparator(false));
$list->add('apple');
$list->add('BANANA');
$list->add('Cherry');
// Result: ['apple', 'BANANA', 'Cherry'] (alphabetically, ignoring case)

// Case-sensitive sorting (default)
$caseSensitiveList = new StringSortedLinkedList(new StringComparator(true));
$caseSensitiveList->add('apple');
$caseSensitiveList->add('Apple');
// Result: ['Apple', 'apple'] (uppercase comes first in ASCII)
```

### DateComparator
Compares DateTime objects:

```php
use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\Comparator\DateComparator;

$dateList = new class(new DateComparator()) extends SortedLinkedList {};
$dateList->add(new DateTime('2024-06-15'));
$dateList->add(new DateTime('2024-01-01'));
$dateList->add(new DateTimeImmutable('2024-03-15'));
// Result: [2024-01-01, 2024-03-15, 2024-06-15]
```

## Custom Comparators

### Using CallableComparator
Create custom comparison logic with a callable:

```php
use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\Comparator\CallableComparator;

// Sort strings by length
$lengthComparator = new CallableComparator(function($a, $b) {
    return strlen($a) <=> strlen($b);
});

$list = new class($lengthComparator) extends SortedLinkedList {};
$list->add('elephant');
$list->add('cat');
$list->add('a');
$list->add('dog');
// Result: ['a', 'cat', 'dog', 'elephant']
```

### Implementing ComparatorInterface
Create a reusable comparator class:

```php
use SortedLinkedList\Comparator\ComparatorInterface;

class PersonAgeComparator implements ComparatorInterface
{
    public function compare(mixed $a, mixed $b): int
    {
        return $a->age <=> $b->age;
    }
}

// Usage
$people = new class(new PersonAgeComparator()) extends SortedLinkedList {};
$people->add((object)['name' => 'Alice', 'age' => 30]);
$people->add((object)['name' => 'Bob', 'age' => 25]);
$people->add((object)['name' => 'Charlie', 'age' => 35]);
// Result sorted by age: Bob(25), Alice(30), Charlie(35)
```

## Advanced Examples

### Multi-Field Sorting
Sort objects by multiple fields with priority:

```php
use SortedLinkedList\Comparator\CallableComparator;

// Sort employees by department, then salary (desc), then name
$employeeComparator = new CallableComparator(function($a, $b) {
    // First by department
    $deptCompare = strcmp($a->department, $b->department);
    if ($deptCompare !== 0) {
        return $deptCompare;
    }

    // Then by salary (descending)
    $salaryCompare = $b->salary <=> $a->salary;
    if ($salaryCompare !== 0) {
        return $salaryCompare;
    }

    // Finally by name
    return strcmp($a->name, $b->name);
});

$employees = new class($employeeComparator) extends SortedLinkedList {};
```

### Null-Safe Comparator
Handle null values gracefully:

```php
$nullSafeComparator = new CallableComparator(function($a, $b) {
    // Nulls come first
    if ($a === null && $b === null) return 0;
    if ($a === null) return -1;
    if ($b === null) return 1;

    // Regular comparison for non-null values
    return $a <=> $b;
});
```

### Using ComparatorFactory
Convenient factory methods for common patterns:

```php
use SortedLinkedList\Comparator\ComparatorFactory;

// Numeric comparison
$numeric = ComparatorFactory::numeric();

// Case-insensitive string comparison
$stringInsensitive = ComparatorFactory::string(false);

// Reverse any comparator
$reverseDate = ComparatorFactory::reverse(ComparatorFactory::date());

// Wrap a closure
$customCallable = ComparatorFactory::callable(function($a, $b) {
    return $a->priority <=> $b->priority;
});
```

### Dynamic Comparator Switching
Change sorting strategy at runtime:

```php
use SortedLinkedList\Comparator\NumericComparator;
use SortedLinkedList\Comparator\ReverseComparator;

$list = new IntegerSortedLinkedList();
$list->add(5);
$list->add(2);
$list->add(8);
// Current order: [2, 5, 8]

// Switch to reverse order (note: doesn't re-sort existing elements)
$list->setComparator(new ReverseComparator(new NumericComparator()));
$list->add(6);
$list->add(1);
// Elements added after comparator change follow new order

// Get current comparator
$currentComparator = $list->getComparator();
```

## Type Safety with Comparators

The library maintains type safety even with custom comparators:

```php
// This will throw InvalidArgumentException if non-integer is added
$intList = new IntegerSortedLinkedList(ComparatorFactory::reverse(ComparatorFactory::numeric()));
$intList->add(5); // OK
$intList->add("5"); // Throws InvalidArgumentException

// StringSortedLinkedList enforces string types
$stringList = new StringSortedLinkedList(ComparatorFactory::string(false));
$stringList->add("hello"); // OK
$stringList->add(123); // Throws InvalidArgumentException
```

## Performance Considerations

- Custom comparators have minimal overhead compared to built-in comparison
- Binary search operations remain O(log n) with custom comparators
- Comparator switching doesn't trigger re-sorting of existing elements
- For best performance with complex comparisons, cache computed values when possible

## Migration from Default Sorting

Existing code continues to work without changes:

```php
// Legacy code - still works
$list = new IntegerSortedLinkedList();
$list->add(5);

// New code with custom comparator
$customList = new IntegerSortedLinkedList($myComparator);
$customList->add(5);
```

The default behavior is preserved for backward compatibility while new functionality is available through the optional comparator parameter.