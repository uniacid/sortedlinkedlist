<?php

declare(strict_types=1);

namespace SortedLinkedList;

use InvalidArgumentException;

/**
 * A sorted linked list implementation specifically for integer values.
 *
 * This class extends the base SortedLinkedList to provide a type-safe
 * implementation for integers with automatic sorting on insertion.
 *
 * @extends SortedLinkedList<int>
 */
class IntegerSortedLinkedList extends SortedLinkedList
{
    /**
     * Compare two integer values for sorting.
     *
     * @param int $a The first integer to compare
     * @param int $b The second integer to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    protected function compare(mixed $a, mixed $b): int
    {
        // Type checking is done in the add method
        // Here we can safely assume both values are integers
        return $a <=> $b;
    }

    /**
     * Add a new integer value to the list in sorted order.
     *
     * @param int $value The integer value to add
     * @throws InvalidArgumentException If the provided value is not an integer
     */
    public function add(mixed $value): void
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_int($value)) {
            throw new InvalidArgumentException('Value must be an integer');
        }

        parent::add($value);
    }

    /**
     * Remove the first occurrence of an integer value from the list.
     *
     * @param int $value The integer value to remove
     * @return bool True if the value was found and removed, false otherwise
     */
    public function remove(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_int($value)) {
            return false;
        }

        return parent::remove($value);
    }

    /**
     * Check if the list contains a specific integer value.
     *
     * @param int $value The integer value to search for
     * @return bool True if the value exists in the list, false otherwise
     */
    public function contains(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_int($value)) {
            return false;
        }

        return parent::contains($value);
    }
}