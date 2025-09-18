<?php

declare(strict_types=1);

namespace SortedLinkedList;

use InvalidArgumentException;
use SortedLinkedList\Comparator\ComparatorInterface;

/**
 * A sorted linked list implementation specifically for float values.
 *
 * This class extends the base SortedLinkedList to provide a type-safe
 * implementation for floating-point numbers with automatic sorting on insertion
 * and proper handling of floating-point precision.
 *
 * @extends SortedLinkedList<float>
 */
class FloatSortedLinkedList extends SortedLinkedList
{
    /**
     * The epsilon value for float comparison to handle precision issues.
     */
    private const EPSILON = 1e-10;

    /**
     * Constructor.
     *
     * @param ComparatorInterface<float>|null $comparator Optional custom comparator for sorting
     */
    public function __construct(?ComparatorInterface $comparator = null)
    {
        parent::__construct($comparator);
    }

    /**
     * Compare two float values for sorting with precision handling.
     *
     * @param float $a The first float to compare
     * @param float $b The second float to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    protected function compare(mixed $a, mixed $b): int
    {
        // Use parent implementation which checks for comparator first
        if ($this->comparator !== null) {
            return parent::compare($a, $b);
        }

        // Type checking is done in the add method
        // Here we can safely assume both values are floats

        // Handle special cases for infinity
        if (is_infinite($a) && is_infinite($b)) {
            return $a <=> $b;
        }

        if (is_infinite($a)) {
            return $a > 0 ? 1 : -1;
        }

        if (is_infinite($b)) {
            return $b > 0 ? -1 : 1;
        }

        // Use epsilon comparison for better float precision handling
        $diff = $a - $b;

        if (abs($diff) < self::EPSILON) {
            return 0;
        }

        return $diff > 0 ? 1 : -1;
    }

    /**
     * Add a new float value to the list in sorted order.
     *
     * @param float $value The float value to add
     * @throws InvalidArgumentException If the provided value is not a float or is NaN
     */
    public function add(mixed $value): void
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_float($value)) {
            throw new InvalidArgumentException('Value must be a float');
        }

        if (is_nan($value)) {
            throw new InvalidArgumentException('NaN values are not allowed');
        }

        parent::add($value);
    }

    /**
     * Remove the first occurrence of a float value from the list.
     *
     * @param float $value The float value to remove
     * @return bool True if the value was found and removed, false otherwise
     */
    public function remove(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_float($value) || is_nan($value)) {
            return false;
        }

        return parent::remove($value);
    }

    /**
     * Check if the list contains a specific float value.
     *
     * @param float $value The float value to search for
     * @return bool True if the value exists in the list, false otherwise
     */
    public function contains(mixed $value): bool
    {
        // Runtime type check is necessary despite PHPDoc hint
        // @phpstan-ignore-next-line
        if (!is_float($value) || is_nan($value)) {
            return false;
        }

        return parent::contains($value);
    }
}
