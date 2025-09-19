<?php

declare(strict_types=1);

namespace SortedLinkedList;

use SortedLinkedList\Comparator\ComparatorInterface;

/**
 * Abstract base class for a sorted linked list data structure.
 *
 * This class provides automatic sorting on insertion, maintaining elements
 * in sorted order based on the comparison logic defined by concrete subclasses
 * or a provided comparator.
 *
 * ## Limitations
 * - **Thread Safety**: This implementation is NOT thread-safe. External synchronization
 *   is required for concurrent access.
 * - **Bulk Operations**: Limited to 100,000 elements per operation to prevent DoS attacks.
 * - **Object Comparison**: Uses spl_object_id() for non-scalar values, which only checks
 *   object identity, not equality.
 * - **Memory Usage**: Each node has overhead for storing references, making this less
 *   memory-efficient than arrays for small datasets.
 * - **Performance**: O(n) insertion/deletion. Consider using arrays with binary search
 *   for mostly-read workloads.
 *
 * @template T
 * @implements \Iterator<int, T>
 * @implements \ArrayAccess<int, T>
 */
abstract class SortedLinkedList implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * Maximum size for bulk operations to prevent DoS attacks.
     */
    protected const MAX_BULK_SIZE = 100000;

    /**
     * Maximum total elements allowed in the list.
     */
    protected const MAX_SIZE = 1000000;

    /**
     * Maximum depth for JSON encoding to prevent DoS attacks.
     */
    protected const MAX_JSON_DEPTH = 10;

    /**
     * The head node of the linked list.
     *
     * @var Node<T>|null
     */
    protected ?Node $head = null;

    /**
     * The number of elements in the list.
     *
     * @var int
     */
    protected int $size = 0;

    /**
     * Optional custom comparator for sorting.
     *
     * @var ComparatorInterface<T>|null
     */
    protected ?ComparatorInterface $comparator = null;

    /**
     * Current position for iterator.
     *
     * @var int
     */
    private int $position = 0;

    /**
     * Current node for iterator.
     *
     * @var Node<T>|null
     */
    private ?Node $currentNode = null;

    /**
     * Cached array for efficient index access.
     *
     * @var array<int, T>|null
     */
    private ?array $indexCache = null;

    /**
     * Constructor.
     *
     * @param ComparatorInterface<T>|null $comparator Optional custom comparator for sorting
     */
    public function __construct(?ComparatorInterface $comparator = null)
    {
        $this->comparator = $comparator;
    }

    /**
     * Set a custom comparator for sorting.
     *
     * Note: Setting a new comparator will not re-sort existing elements.
     * The new comparator will only affect future insertions.
     *
     * @param ComparatorInterface<T>|null $comparator The comparator to use, or null to use default
     */
    public function setComparator(?ComparatorInterface $comparator): void
    {
        $this->comparator = $comparator;
    }

    /**
     * Get the current comparator.
     *
     * @return ComparatorInterface<T>|null The current comparator, or null if using default
     */
    public function getComparator(): ?ComparatorInterface
    {
        return $this->comparator;
    }

    /**
     * Compare two values for sorting.
     *
     * This method can be overridden by concrete subclasses to define
     * the sorting order for specific data types. If a comparator is provided,
     * it will be used instead of this method.
     *
     * @param T $a The first value to compare
     * @param T $b The second value to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    protected function compare(mixed $a, mixed $b): int
    {
        if ($this->comparator !== null) {
            return $this->comparator->compare($a, $b);
        }

        // Default implementation for backward compatibility
        // Concrete subclasses should override this if not using a comparator
        return $a <=> $b;
    }

    /**
     * Add a new value to the list in sorted order.
     *
     * The value will be inserted at the appropriate position to maintain
     * the sorted order of the list.
     *
     * Time complexity: O(n)
     *
     * @param T $value The value to add
     */
    public function add(mixed $value): void
    {
        // Check maximum size limit
        if ($this->size >= self::MAX_SIZE) {
            throw new \OverflowException(
                sprintf('List has reached maximum size of %d elements', self::MAX_SIZE)
            );
        }

        $newNode = new Node($value);

        // If list is empty or value should be first
        if ($this->head === null || $this->compare($value, $this->head->getValue()) <= 0) {
            $newNode->setNext($this->head);
            $this->head = $newNode;
            $this->size++;
            $this->invalidateCache();
            return;
        }

        // Find the insertion point
        $current = $this->head;
        while (
            $current->getNext() !== null &&
               $this->compare($value, $current->getNext()->getValue()) > 0
        ) {
            $current = $current->getNext();
        }

        // Insert the new node
        $newNode->setNext($current->getNext());
        $current->setNext($newNode);
        $this->size++;
        $this->invalidateCache();
    }

    /**
     * Remove the first occurrence of a value from the list.
     *
     * Time complexity: O(n)
     *
     * @param T $value The value to remove
     * @return bool True if the value was found and removed, false otherwise
     */
    public function remove(mixed $value): bool
    {
        // Empty list
        if ($this->head === null) {
            return false;
        }

        // Check if it's the first element
        if ($this->compare($value, $this->head->getValue()) === 0) {
            $this->head = $this->head->getNext();
            $this->size--;
            $this->invalidateCache();
            return true;
        }

        // Search for the value in the rest of the list
        $current = $this->head;
        while ($current->getNext() !== null) {
            if ($this->compare($value, $current->getNext()->getValue()) === 0) {
                // Remove the node
                $current->setNext($current->getNext()->getNext());
                $this->size--;
                $this->invalidateCache();
                return true;
            }
            $current = $current->getNext();
        }

        return false;
    }

    /**
     * Check if the list contains a specific value.
     *
     * Time complexity: O(n)
     *
     * @param T $value The value to search for
     * @return bool True if the value exists in the list, false otherwise
     */
    public function contains(mixed $value): bool
    {
        $current = $this->head;

        while ($current !== null) {
            if ($this->compare($value, $current->getValue()) === 0) {
                return true;
            }
            $current = $current->getNext();
        }

        return false;
    }

    /**
     * Get the number of elements in the list.
     *
     * Time complexity: O(1)
     *
     * @return int The size of the list
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Remove all elements from the list.
     *
     * Time complexity: O(1)
     */
    public function clear(): void
    {
        $this->head = null;
        $this->size = 0;
        $this->invalidateCache();
    }

    /**
     * Perform binary search to find the index of a value.
     *
     * Time complexity: O(log n)
     *
     * @param T $value The value to search for
     * @return int The index of the value, or -1 if not found
     */
    public function binarySearch(mixed $value): int
    {
        if ($this->size === 0) {
            return -1;
        }

        $this->buildIndexCache();
        if ($this->indexCache === null) {
            return -1;
        }

        $left = 0;
        $right = $this->size - 1;

        while ($left <= $right) {
            $mid = $left + intval(($right - $left) / 2);
            $comparison = $this->compare($value, $this->indexCache[$mid]);

            if ($comparison === 0) {
                return $mid;
            } elseif ($comparison < 0) {
                $right = $mid - 1;
            } else {
                $left = $mid + 1;
            }
        }

        return -1;
    }

    /**
     * Get the index of a value in the list.
     *
     * Uses binary search for O(log n) performance.
     *
     * @param T $value The value to find
     * @return int The index of the value, or -1 if not found
     */
    public function indexOf(mixed $value): int
    {
        return $this->binarySearch($value);
    }

    /**
     * Build or rebuild the index cache for O(1) access.
     */
    private function buildIndexCache(): void
    {
        if ($this->indexCache !== null) {
            return;
        }

        $this->indexCache = [];
        $current = $this->head;
        while ($current !== null) {
            $this->indexCache[] = $current->getValue();
            $current = $current->getNext();
        }
    }

    /**
     * Invalidate the index cache.
     */
    private function invalidateCache(): void
    {
        $this->indexCache = null;
    }

    /**
     * Get a unique key for a value, safe from security vulnerabilities.
     *
     * @param mixed $value The value to get a key for
     * @return string A unique string key for the value
     */
    protected function getValueKey(mixed $value): string
    {
        if (is_scalar($value)) {
            return (string)$value;
        }

        if (is_object($value)) {
            return (string)spl_object_id($value);
        }

        // For arrays, use a hash instead of JSON encoding
        // This prevents memory exhaustion attacks
        if (is_array($value)) {
            // Create a hash of the array structure
            // Limited depth traversal to prevent DoS
            return 'array_' . md5(serialize($this->limitArrayDepth($value, 3)));
        }

        // For resources and other types
        if (is_resource($value)) {
            return 'resource_' . get_resource_id($value);
        }

        // Fallback for unknown types
        return 'unknown_' . md5(gettype($value));
    }

    /**
     * Limit array depth for safe serialization.
     *
     * @param array<mixed> $array The array to limit
     * @param int $maxDepth Maximum depth allowed
     * @param int $currentDepth Current recursion depth
     * @return array<mixed> Array with limited depth
     */
    private function limitArrayDepth(array $array, int $maxDepth, int $currentDepth = 0): array
    {
        if ($currentDepth >= $maxDepth) {
            return ['__truncated__' => true];
        }

        $result = [];
        $count = 0;
        foreach ($array as $key => $value) {
            if ($count++ > 100) {  // Limit array size at each level
                $result['__truncated__'] = true;
                break;
            }

            if (is_array($value)) {
                $result[$key] = $this->limitArrayDepth($value, $maxDepth, $currentDepth + 1);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    // Iterator interface methods

    /**
     * Rewind the iterator to the first element.
     */
    public function rewind(): void
    {
        $this->position = 0;
        $this->currentNode = $this->head;
    }

    /**
     * Get the current element.
     *
     * @return T|null
     */
    public function current(): mixed
    {
        return $this->currentNode?->getValue();
    }

    /**
     * Get the current position.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Move to the next element.
     */
    public function next(): void
    {
        if ($this->currentNode !== null) {
            $this->currentNode = $this->currentNode->getNext();
            $this->position++;
        }
    }

    /**
     * Check if the current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->currentNode !== null;
    }

    /**
     * Move to the previous element.
     */
    public function prev(): void
    {
        if ($this->position <= 0) {
            return;
        }

        $this->position--;

        if ($this->position === 0) {
            $this->currentNode = $this->head;
            return;
        }

        // Find the node at position - 1
        $current = $this->head;
        for ($i = 0; $i < $this->position && $current !== null; $i++) {
            $current = $current->getNext();
        }
        $this->currentNode = $current;
    }

    /**
     * Move to the last element.
     */
    public function end(): void
    {
        if ($this->head === null) {
            $this->position = 0;
            $this->currentNode = null;
            return;
        }

        $current = $this->head;
        $pos = 0;

        while ($current->getNext() !== null) {
            $current = $current->getNext();
            $pos++;
        }

        $this->currentNode = $current;
        $this->position = $pos;
    }

    /**
     * Seek to a specific position.
     *
     * @param int $position The position to seek to
     * @throws \OutOfBoundsException If position is invalid
     */
    public function seek(int $position): void
    {
        if ($position < 0 || $position >= $this->size) {
            throw new \OutOfBoundsException('Position is out of bounds');
        }

        $this->position = $position;
        $current = $this->head;

        for ($i = 0; $i < $position && $current !== null; $i++) {
            $current = $current->getNext();
        }

        $this->currentNode = $current;
    }

    /**
     * Check if there is a previous element.
     *
     * @return bool
     */
    public function hasPrev(): bool
    {
        return $this->position > 0;
    }

    /**
     * Check if there is a next element.
     *
     * @return bool
     */
    public function hasNext(): bool
    {
        return $this->currentNode !== null && $this->currentNode->getNext() !== null;
    }

    // ArrayAccess interface methods

    /**
     * Check if an offset exists.
     *
     * @param mixed $offset The offset to check
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->size;
    }

    /**
     * Get value at offset.
     *
     * @param mixed $offset The offset to retrieve
     * @return T
     * @throws \OutOfBoundsException If offset is invalid
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!is_int($offset) || $offset < 0 || $offset >= $this->size) {
            throw new \OutOfBoundsException('Array offset is out of bounds');
        }

        $this->buildIndexCache();
        if ($this->indexCache === null) {
            throw new \RuntimeException('Internal cache error');
        }
        return $this->indexCache[$offset];
    }

    /**
     * Set value at offset.
     *
     * Note: This maintains sorted order, so the value may not end up at the specified offset.
     *
     * @param mixed $offset The offset (ignored for sorted list)
     * @param T $value The value to add
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        // Ignore offset and add value in sorted position
        $this->add($value);
    }

    /**
     * Unset value at offset.
     *
     * @param mixed $offset The offset to unset
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!is_int($offset) || $offset < 0 || $offset >= $this->size) {
            return;
        }

        $this->buildIndexCache();
        if ($this->indexCache !== null && isset($this->indexCache[$offset])) {
            $this->remove($this->indexCache[$offset]);
        }
    }

    // Countable interface method

    /**
     * Count the number of elements.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->size;
    }

    // Bulk Operations

    /**
     * Add all values from an iterable to the list.
     *
     * @param iterable<T> $values The values to add
     */
    public function addAll(iterable $values): void
    {
        // Convert to array for potential optimization
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) === 0) {
            return;
        }

        // Check for maximum size to prevent DoS
        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException('Bulk operation exceeds maximum size of ' . self::MAX_BULK_SIZE);
        }

        // Optimization: If list is empty, sort array and build list directly
        if ($this->head === null && count($values) > 1) {
            // Sort the array using the comparator
            usort($values, [$this, 'compare']);

            // Build the list from sorted values
            $firstValue = array_shift($values);
            if ($firstValue === null) {
                return;
            }
            $this->head = new Node($firstValue);
            $this->size = 1;

            $current = $this->head;
            foreach ($values as $value) {
                $newNode = new Node($value);
                $current->setNext($newNode);
                $current = $newNode;
                $this->size++;
            }

            $this->invalidateCache();
        } else {
            // Regular insertion for non-empty lists
            // We'll manually add nodes to avoid multiple cache invalidations
            foreach ($values as $value) {
                $newNode = new Node($value);

                // If value should be first
                if ($this->head === null || $this->compare($value, $this->head->getValue()) <= 0) {
                    $newNode->setNext($this->head);
                    $this->head = $newNode;
                    $this->size++;
                } else {
                    // Find insertion point
                    $current = $this->head;
                    while (
                        $current->getNext() !== null &&
                        $this->compare($value, $current->getNext()->getValue()) > 0
                    ) {
                        $current = $current->getNext();
                    }

                    // Insert the new node
                    $newNode->setNext($current->getNext());
                    $current->setNext($newNode);
                    $this->size++;
                }
            }
            // Invalidate cache only once after all additions
            $this->invalidateCache();
        }
    }

    /**
     * Remove all occurrences of the specified values from the list.
     *
     * @param iterable<T> $values The values to remove
     */
    public function removeAll(iterable $values): void
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) === 0) {
            return;
        }

        // Check for maximum size to prevent DoS
        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException('Bulk operation exceeds maximum size of ' . self::MAX_BULK_SIZE);
        }

        // Create a set for O(1) lookups
        $valuesToRemove = array_flip(array_map(
            fn($v) => $this->getValueKey($v),
            $values
        ));

        $current = $this->head;
        $previous = null;

        while ($current !== null) {
            $currentValueKey = $this->getValueKey($current->getValue());

            if (isset($valuesToRemove[$currentValueKey])) {
                // Remove this node
                if ($previous === null) {
                    // Removing head
                    $this->head = $current->getNext();
                } else {
                    $previous->setNext($current->getNext());
                }
                $this->size--;

                // Don't update previous, as we removed current
                $current = $previous === null ? $this->head : $previous->getNext();
            } else {
                // Move forward
                $previous = $current;
                $current = $current->getNext();
            }
        }

        // Invalidate cache once after all removals
        $this->invalidateCache();
    }

    /**
     * Retain only the values that are in the specified collection.
     *
     * @param iterable<T> $values The values to retain
     */
    public function retainAll(iterable $values): void
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) === 0) {
            $this->clear();
            return;
        }

        // Check for maximum size to prevent DoS
        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException('Bulk operation exceeds maximum size of ' . self::MAX_BULK_SIZE);
        }

        // Create a set for O(1) lookups
        $valuesToRetain = array_flip(array_map(
            fn($v) => $this->getValueKey($v),
            $values
        ));

        $current = $this->head;
        $previous = null;

        while ($current !== null) {
            $currentValueKey = $this->getValueKey($current->getValue());

            if (!isset($valuesToRetain[$currentValueKey])) {
                // Remove this node
                if ($previous === null) {
                    // Removing head
                    $this->head = $current->getNext();
                } else {
                    $previous->setNext($current->getNext());
                }
                $this->size--;

                // Don't update previous, as we removed current
                $current = $previous === null ? $this->head : $previous->getNext();
            } else {
                // Move forward
                $previous = $current;
                $current = $current->getNext();
            }
        }

        // Invalidate cache once after all operations
        $this->invalidateCache();
    }

    /**
     * Check if the list contains all the specified values.
     *
     * @param iterable<T> $values The values to check
     * @return bool True if all values are present, false otherwise
     */
    public function containsAll(iterable $values): bool
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) === 0) {
            return true;
        }

        foreach ($values as $value) {
            if (!$this->contains($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Convert the list to an array.
     *
     * @return array<int, T>
     */
    public function toArray(): array
    {
        $this->buildIndexCache();
        return $this->indexCache ?? [];
    }

    /**
     * Create a new sorted linked list from an array.
     *
     * @param array<T> $values The values to add
     * @param ComparatorInterface<T>|null $comparator Optional custom comparator
     * @return static
     */
    public static function fromArray(array $values, ?ComparatorInterface $comparator = null): static
    {
        /** @var static $list */
        /** @phpstan-ignore-next-line */
        $list = new static($comparator);
        $list->addAll($values);
        return $list;
    }

    // Collection Transformation Methods

    /**
     * Apply a transformation function to each element.
     *
     * @param callable(T): T $callback The transformation function
     * @return static A new list with transformed values
     */
    public function map(callable $callback): static
    {
        /** @var static $newList */
        /** @phpstan-ignore-next-line */
        $newList = new static($this->comparator);

        $current = $this->head;
        while ($current !== null) {
            $newList->add($callback($current->getValue()));
            $current = $current->getNext();
        }

        return $newList;
    }

    /**
     * Filter elements based on a predicate.
     *
     * @param callable(T): bool $predicate The filter predicate
     * @return static A new list with filtered values
     */
    public function filter(callable $predicate): static
    {
        /** @var static $newList */
        /** @phpstan-ignore-next-line */
        $newList = new static($this->comparator);

        $current = $this->head;
        while ($current !== null) {
            $value = $current->getValue();
            if ($predicate($value)) {
                $newList->add($value);
            }
            $current = $current->getNext();
        }

        return $newList;
    }

    /**
     * Reduce the list to a single value.
     *
     * @template R
     * @param callable(R, T): R $callback The reduction function
     * @param R $initial The initial value
     * @return R The reduced value
     */
    public function reduce(callable $callback, mixed $initial): mixed
    {
        $accumulator = $initial;

        $current = $this->head;
        while ($current !== null) {
            $accumulator = $callback($accumulator, $current->getValue());
            $current = $current->getNext();
        }

        return $accumulator;
    }
}
