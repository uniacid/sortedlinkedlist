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
 */
abstract class SortedLinkedList
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
            return true;
        }

        // Search for the value in the rest of the list
        $current = $this->head;
        while ($current->getNext() !== null) {
            if ($this->compare($value, $current->getNext()->getValue()) === 0) {
                // Remove the node
                $current->setNext($current->getNext()->getNext());
                $this->size--;
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
    }
}