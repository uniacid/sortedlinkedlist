<?php

declare(strict_types=1);

namespace SortedLinkedList;

use InvalidArgumentException;

/**
 * A sorted linked list implementation specifically for string values.
 *
 * This class extends the base SortedLinkedList to provide a type-safe
 * implementation for strings with automatic sorting on insertion using
 * lexicographic (dictionary) ordering.
 *
 * @extends SortedLinkedList<string>
 */
class StringSortedLinkedList extends SortedLinkedList
{
    /**
     * Compare two string values for sorting using lexicographic ordering.
     *
     * @param string $a The first string to compare
     * @param string $b The second string to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    protected function compare(mixed $a, mixed $b): int
    {
        // Type checking is done in the add method
        // Both parameters are guaranteed to be strings when called
        /** @var string $a */
        /** @var string $b */
        return strcmp($a, $b);
    }

    /**
     * Add a new string value to the list in sorted order.
     *
     * @param string $value The string value to add
     * @throws InvalidArgumentException If the provided value is not a string
     */
    public function add(mixed $value): void
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_string($value)) {
            throw new InvalidArgumentException('Value must be a string');
        }

        parent::add($value);
    }

    /**
     * Remove the first occurrence of a string value from the list.
     *
     * @param string $value The string value to remove
     * @return bool True if the value was found and removed, false otherwise
     */
    public function remove(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_string($value)) {
            return false;
        }

        return parent::remove($value);
    }

    /**
     * Check if the list contains a specific string value.
     *
     * @param string $value The string value to search for
     * @return bool True if the value exists in the list, false otherwise
     */
    public function contains(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_string($value)) {
            return false;
        }

        return parent::contains($value);
    }
}