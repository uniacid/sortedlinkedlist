<?php

declare(strict_types=1);

namespace SortedLinkedList;

/**
 * Abstract base class for a sorted linked list data structure.
 *
 * This class provides automatic sorting on insertion, maintaining elements
 * in sorted order based on the comparison logic defined by concrete subclasses.
 *
 * @template T
 * @implements \Iterator<int, T>
 * @implements \ArrayAccess<int, T>
 */
abstract class SortedLinkedList implements \Iterator, \ArrayAccess, \Countable
{
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
     * Compare two values for sorting.
     *
     * This method must be implemented by concrete subclasses to define
     * the sorting order for specific data types.
     *
     * @param T $a The first value to compare
     * @param T $b The second value to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    abstract protected function compare(mixed $a, mixed $b): int;

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
        while ($current->getNext() !== null &&
               $this->compare($value, $current->getNext()->getValue()) > 0) {
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
            throw new \OutOfBoundsException("Position $position is out of bounds");
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
            $offsetStr = is_scalar($offset) ? (string)$offset : 'invalid';
            throw new \OutOfBoundsException("Offset " . $offsetStr . " is out of bounds");
        }

        $this->buildIndexCache();
        if ($this->indexCache === null) {
            throw new \OutOfBoundsException("Cache build failed");
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
}