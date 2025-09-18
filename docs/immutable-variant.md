# Immutable Sorted Linked List

## Overview

The `ImmutableSortedLinkedList` class provides an immutable variant of the SortedLinkedList data structure. All operations that would normally mutate the list instead return a new instance, preserving the original list unchanged.

## Features

- **Complete Immutability**: All operations return new instances
- **Type Safety**: Full PHPStan level 8 compliance
- **Copy-on-Write Semantics**: Efficient cloning of data structures
- **Thread Safety**: Immutability guarantees safe concurrent access
- **Familiar API**: Similar methods to the mutable variant with `with*` prefix

## Usage

### Creating an Immutable List

```php
use SortedLinkedList\ImmutableSortedLinkedList;
use SortedLinkedList\IntegerImmutableSortedLinkedList;

// Create an empty immutable list for integers
$list = new IntegerImmutableSortedLinkedList();

// Create from an array
$list = IntegerImmutableSortedLinkedList::fromArray([3, 1, 4, 1, 5, 9]);
```

### Adding Elements

```php
$list1 = new IntegerImmutableSortedLinkedList();
$list2 = $list1->withAdd(5);  // Returns new instance
$list3 = $list2->withAdd(3);  // Returns another new instance

// Original lists remain unchanged
echo $list1->size(); // 0
echo $list2->size(); // 1
echo $list3->size(); // 2

// Method chaining
$list = $list1
    ->withAdd(5)
    ->withAdd(3)
    ->withAdd(7)
    ->withAdd(1);
```

### Removing Elements

```php
$list1 = IntegerImmutableSortedLinkedList::fromArray([1, 3, 5, 7, 9]);
$list2 = $list1->withRemove(5);

echo implode(', ', $list1->toArray()); // 1, 3, 5, 7, 9
echo implode(', ', $list2->toArray()); // 1, 3, 7, 9
```

### Bulk Operations

```php
$list1 = new IntegerImmutableSortedLinkedList();

// Add multiple values
$list2 = $list1->withAddAll([3, 1, 4, 1, 5]);

// Remove multiple values
$list3 = $list2->withRemoveAll([1, 3]);

// Retain only specific values
$list4 = $list2->withRetainAll([1, 3, 5]);

// Clear all values
$list5 = $list2->withClear();
```

### Transformation Operations

```php
$list = IntegerImmutableSortedLinkedList::fromArray([1, 2, 3, 4, 5]);

// Map transformation
$doubled = $list->map(fn($x) => $x * 2);
// Result: [2, 4, 6, 8, 10]

// Filter operation
$evens = $list->filter(fn($x) => $x % 2 === 0);
// Result: [2, 4]

// Reduce operation
$sum = $list->reduce(fn($acc, $x) => $acc + $x, 0);
// Result: 15
```

### Custom Comparators

```php
use SortedLinkedList\Comparator\NumericComparator;

// Change comparator (re-sorts elements)
$list1 = IntegerImmutableSortedLinkedList::fromArray([3, 1, 5, 2, 4]);

// Create reverse comparator
$reverseComparator = new class implements ComparatorInterface {
    public function compare($a, $b): int {
        return $b <=> $a; // Reverse order
    }
};

$list2 = $list1->withComparator($reverseComparator);
// list1: [1, 2, 3, 4, 5]
// list2: [5, 4, 3, 2, 1]
```

## Mutating Methods Throw Exceptions

Attempting to use mutating methods from the parent class will throw exceptions:

```php
$list = new IntegerImmutableSortedLinkedList();

// These will throw BadMethodCallException:
$list->add(5);        // Use withAdd() instead
$list->remove(5);     // Use withRemove() instead
$list->clear();       // Use withClear() instead
$list->addAll([]);    // Use withAddAll() instead
$list->removeAll([]); // Use withRemoveAll() instead
$list->retainAll([]); // Use withRetainAll() instead
```

## Benefits of Immutability

1. **Thread Safety**: No synchronization needed for concurrent access
2. **Predictable Code**: No unexpected side effects from method calls
3. **Time Travel**: Keep references to previous states easily
4. **Functional Programming**: Works well with functional programming patterns
5. **Debugging**: Easier to track state changes

## Performance Considerations

- Each operation creates a new instance with copied nodes
- Best for scenarios where immutability benefits outweigh copying costs
- Consider using the mutable variant for performance-critical code with single ownership

## Implementation Notes

The implementation uses:
- Copy-on-write semantics for all modifications
- Full node cloning to ensure complete immutability
- Efficient array-based operations for bulk modifications
- Type-safe generics with PHPDoc annotations