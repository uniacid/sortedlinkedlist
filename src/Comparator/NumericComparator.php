<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use InvalidArgumentException;

/**
 * Comparator for numeric values (integers and floats).
 *
 * This comparator performs standard numeric comparison and validates
 * that both values are numeric before comparing.
 *
 * @implements ComparatorInterface<int|float>
 */
class NumericComparator implements ComparatorInterface
{
    /**
     * Compare two numeric values.
     *
     * @param int|float $a The first numeric value
     * @param int|float $b The second numeric value
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     * @throws InvalidArgumentException If either value is not numeric
     */
    public function compare(mixed $a, mixed $b): int
    {
        // Runtime validation for type safety
        // @phpstan-ignore-next-line
        if (!is_numeric($a) || !is_numeric($b)) {
            throw new InvalidArgumentException('NumericComparator can only compare numeric values');
        }

        // Use spaceship operator for numeric comparison
        return $a <=> $b;
    }
}