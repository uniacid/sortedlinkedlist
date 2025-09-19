# SortedLinkedList

A high-performance, type-safe, automatically-sorted linked list data structure for PHP with advanced features including binary search optimization, custom comparators, bulk operations, and immutable variants.

[![CI Status](https://github.com/uniacid/sortedlinkedlist/actions/workflows/ci.yml/badge.svg)](https://github.com/uniacid/sortedlinkedlist/actions)
[![Coverage Status](https://coveralls.io/repos/github/uniacid/sortedlinkedlist/badge.svg?branch=master)](https://coveralls.io/github/uniacid/sortedlinkedlist?branch=master)
[![Latest Stable Version](https://poser.pugx.org/uniacid/sortedlinkedlist/v/stable)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![Total Downloads](https://poser.pugx.org/uniacid/sortedlinkedlist/downloads)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![License](https://poser.pugx.org/uniacid/sortedlinkedlist/license)](https://packagist.org/packages/uniacid/sortedlinkedlist)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.1-blue)](https://www.php.net)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![PSR-12](https://img.shields.io/badge/PSR-12-blue)](https://www.php-fig.org/psr/psr-12/)

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Performance Characteristics](#performance-characteristics)
- [Advanced Usage](#advanced-usage)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Type-Safe Implementations**: Dedicated classes for Integer, String, and Float types
- **Automatic Sorting**: Elements are automatically maintained in sorted order
- **Binary Search Optimization**: O(log n) search complexity for large datasets
- **Custom Comparators**: Flexible sorting with built-in and custom comparators
- **Bulk Operations**: Efficient batch operations (addAll, removeAll, retainAll)
- **Immutable Variant**: Thread-safe implementation with structural sharing
- **Iterator Support**: Full PHP Iterator and ArrayAccess interface compliance
- **Collection Transformations**: Map, filter, and reduce operations
- **Performance Tracking**: Built-in statistics for performance analysis

## Requirements

- PHP 8.1 or higher
- Composer for dependency management
- No runtime dependencies (zero-dependency library)

## Installation

```bash
composer require uniacid/sortedlinkedlist
```

## Quick Start

```php
use SortedLinkedList\IntegerSortedLinkedList;

$list = new IntegerSortedLinkedList();
$list->add(5);
$list->add(2);
$list->add(8);
$list->add(1);

// Elements are automatically sorted
foreach ($list as $value) {
    echo $value . " "; // Output: 1 2 5 8
}

// Binary search for efficient lookups
$index = $list->binarySearch(5); // Returns index of element

// Bulk operations
$list->addAll([3, 7, 4]);
$filtered = $list->filter(fn($v) => $v > 3);
$doubled = $list->map(fn($v) => $v * 2);
```

## Usage Examples

### Basic Operations with Different Types

```php
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

// Integer list
$intList = new IntegerSortedLinkedList();
$intList->add(42);
$intList->add(17);
$intList->add(99);
echo $intList->first(); // 17
echo $intList->last();  // 99

// String list
$stringList = new StringSortedLinkedList();
$stringList->addAll(['banana', 'apple', 'cherry']);
foreach ($stringList as $fruit) {
    echo $fruit . ' '; // apple banana cherry
}

// Float list
$floatList = new FloatSortedLinkedList();
$floatList->addAll([3.14, 1.41, 2.71]);
echo $floatList->get(1); // 2.71 (second element after sorting)
```

### Binary Search and Contains

```php
$list = new IntegerSortedLinkedList();
$list->addAll(range(1, 1000));

// Binary search (fast for large lists)
$index = $list->binarySearch(500);
if ($index >= 0) {
    echo "Found at index: " . $index;
}

// Contains check
if ($list->contains(750)) {
    echo "List contains 750";
}

// Find first occurrence of value >= 400
$index = $list->binarySearchInsertPosition(400);
echo "Insert position for 400: " . $index;
```

### Collection Operations

```php
$list = new IntegerSortedLinkedList();
$list->addAll([5, 2, 8, 1, 9, 3, 7]);

// Filter elements
$filtered = $list->filter(fn($v) => $v > 5);
// Result: [7, 8, 9]

// Map transformation
$doubled = $list->map(fn($v) => $v * 2);
// Result: [2, 4, 6, 10, 14, 16, 18]

// Reduce to single value
$sum = $list->reduce(fn($carry, $item) => $carry + $item, 0);
echo "Sum: " . $sum; // 35

// Slice operations
$slice = $list->slice(2, 3); // Get 3 elements starting from index 2
// Result: [3, 5, 7]
```

### Bulk Operations

```php
$list1 = new StringSortedLinkedList();
$list1->addAll(['apple', 'banana', 'cherry']);

$list2 = new StringSortedLinkedList();
$list2->addAll(['banana', 'date', 'elderberry']);

// Remove all elements that exist in list2
$list1->removeAll($list2);
// list1 now contains: ['apple', 'cherry']

// Retain only elements that exist in both
$list3 = new StringSortedLinkedList();
$list3->addAll(['apple', 'banana', 'cherry']);
$list3->retainAll(['banana', 'cherry', 'date']);
// list3 now contains: ['banana', 'cherry']
```

### Array Access Interface

```php
$list = new IntegerSortedLinkedList();
$list->addAll([30, 10, 20]);

// Array-like access
echo $list[0];  // 10 (first sorted element)
echo $list[1];  // 20
echo $list[2];  // 30

// Check if index exists
if (isset($list[1])) {
    echo "Index 1 exists";
}

// Note: Setting values via array access maintains sort order
$list[3] = 15;  // Adds 15 to the list in sorted position
```

### Iterator and Foreach

```php
$list = new StringSortedLinkedList();
$list->addAll(['zebra', 'alpha', 'beta']);

// Standard foreach
foreach ($list as $index => $value) {
    echo "$index: $value\n";
}
// Output:
// 0: alpha
// 1: beta
// 2: zebra

// Manual iteration
$list->rewind();
while ($list->valid()) {
    echo $list->current() . "\n";
    $list->next();
}
```

### Converting to Array

```php
$list = new FloatSortedLinkedList();
$list->addAll([3.14, 1.41, 2.71, 1.73]);

// Convert to array
$array = $list->toArray();
// Result: [1.41, 1.73, 2.71, 3.14]

// Get values only (same as toArray)
$values = $list->values();

// Check if empty
if (!$list->isEmpty()) {
    echo "List has " . $list->size() . " elements";
}

// Clear all elements
$list->clear();
echo $list->isEmpty() ? "Empty" : "Not empty"; // Empty
```

## Documentation

- [API Documentation](https://uniacid.github.io/sortedlinkedlist/) - Complete class and method references
- [Usage Examples](#advanced-usage) - Detailed examples for all features
- [Performance Guide](#performance-characteristics) - Benchmarks and optimization tips
- [Contributing Guidelines](CONTRIBUTING.md) - How to contribute to the project

## Performance Characteristics

### Time Complexity

| Operation | SortedLinkedList | Native Array (sorted) | Notes |
|-----------|-----------------|----------------------|-------|
| Add (single) | O(n) | O(n) | List maintains sort order |
| Add (bulk) | O(n*m) | O(n+m) + O(n log n) | m = items to add |
| Search (binary) | O(log n) | O(log n)* | *Requires manual implementation |
| Search (contains) | O(n) | O(n) | Linear search fallback |
| Remove | O(n) | O(n) | Includes search time |
| Iteration | O(n) | O(n) | Similar performance |
| Size | O(1) | O(1) | Cached value |
| Clear | O(1) | O(1) | Reset references |

### Memory Usage

| Data Structure | Memory Overhead | Notes |
|----------------|----------------|-------|
| SortedLinkedList | ~2.5x | Node objects + references |
| ImmutableSortedLinkedList | ~1.5x per version | Structural sharing reduces overhead |
| Native Array | 1x (baseline) | Contiguous memory |

### Benchmark Results

Performance benchmarks run on PHP 8.3 with 1000 elements:

#### Add Operations
```
SortedLinkedList:     245.3 μs (±3.2%)
Native Array + sort:  112.7 μs (±2.1%)
Overhead:             2.18x
```

#### Search Operations (Binary vs Linear)
```
Binary Search (n=1000):  8.2 μs (±1.5%)
Linear Search (n=1000):  142.6 μs (±2.8%)
Speedup:                 17.4x for large datasets
```

#### Iterator Performance
```
SortedLinkedList foreach:  89.3 μs (±1.9%)
Native array foreach:       31.2 μs (±1.2%)
Overhead:                   2.86x
```

#### Bulk Operations
```
addAll (500 items):     312.5 μs (±2.5%)
removeAll (250 items):  198.7 μs (±2.1%)
filter (50%):           156.3 μs (±1.8%)
map transformation:     178.9 μs (±2.3%)
```

### When to Use SortedLinkedList

**Use SortedLinkedList when:**
- You need automatic sorting with every insertion
- Binary search optimization is important
- You need custom comparison logic
- Immutability and thread safety are required
- You frequently filter/map/reduce collections
- Type safety is important

**Use Native Arrays when:**
- Memory usage is critical
- Data is inserted in bulk then sorted once
- Simple numeric indices are sufficient
- Maximum iteration speed is required

## Advanced Usage

### Custom Comparators

```php
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\Comparator\DateComparator;
use SortedLinkedList\Comparator\ObjectComparator;
use SortedLinkedList\Comparator\ReverseComparator;
use SortedLinkedList\Comparator\ChainComparator;

// Date sorting
$dateComparator = new DateComparator('Y-m-d');
$dateList = new ImmutableSortedLinkedList($dateComparator);
$dateList = $dateList->addAll(['2024-03-15', '2024-01-10', '2024-02-20']);

// Object property sorting
$users = [
    (object)['name' => 'Alice', 'age' => 30],
    (object)['name' => 'Bob', 'age' => 25],
];
$ageComparator = new ObjectComparator('age');
$userList = new ImmutableSortedLinkedList($ageComparator);
$userList = $userList->addAll($users);

// Reverse order
$reverseNumeric = new ReverseComparator(new NumericComparator());
$reverseList = new ImmutableSortedLinkedList($reverseNumeric);

// Multiple criteria (sort by age, then name)
$chainedComparator = new ChainComparator([
    new ObjectComparator('age'),
    new ObjectComparator('name')
]);
```

### Immutable Operations

```php
use SortedLinkedList\ImmutableSortedLinkedList;

$original = new ImmutableSortedLinkedList(new NumericComparator());
$original = $original->addAll([1, 2, 3, 4, 5]);

// Each operation returns a new instance
$version1 = $original->add(6);
$version2 = $original->remove(3);
$version3 = $version1->filter(fn($v) => $v % 2 === 0);

// All versions are independent
echo $original->size(); // 5
echo $version1->size(); // 6
echo $version2->size(); // 4
echo $version3->size(); // 3 (only even numbers from version1)
```

### Performance Monitoring

```php
$list = new IntegerSortedLinkedList();
$list->resetStats();

// Perform operations
for ($i = 0; $i < 1000; $i++) {
    $list->add(random_int(1, 10000));
}

for ($i = 0; $i < 100; $i++) {
    $list->binarySearch(random_int(1, 10000));
}

// Get performance statistics
$stats = $list->getStats();
echo "Comparisons: " . $stats['comparisons'] . "\n";
echo "Node traversals: " . $stats['traversals'] . "\n";
```

## Running Benchmarks

```bash
# Run all benchmarks
composer bench

# Run specific benchmark group
vendor/bin/phpbench run --group=search

# Compare with baseline
composer bench-compare

# Memory usage analysis
composer bench-memory

# Generate HTML report
vendor/bin/phpbench run --report=html --output=html
```

## Testing

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run PHPStan analysis
composer analyse

# Run everything
composer check
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. Make sure to:

1. Add tests for new features
2. Update benchmarks if performance-related
3. Run `composer check` before submitting
4. Follow PSR-12 coding standards

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Performance Optimization Tips

1. **Use Binary Search**: For large datasets (>100 elements), always prefer `binarySearch()` over `contains()`
2. **Bulk Operations**: Use `addAll()` instead of multiple `add()` calls for better performance
3. **Immutable Lists**: Use for concurrent access or when you need to maintain multiple versions
4. **Custom Comparators**: Implement efficient comparison logic to minimize operations
5. **Stats Monitoring**: Use `getStats()` in development to identify performance bottlenecks

## Roadmap

- [ ] Persistent data structure variant
- [ ] Lazy evaluation for map/filter operations
- [ ] Parallel processing for bulk operations
- [ ] Memory-mapped file backend for huge datasets
- [ ] Redis/Memcached adapters
- [ ] Serialization support for data persistence