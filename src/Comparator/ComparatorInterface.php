<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

/**
 * Interface for comparing two values.
 *
 * Implementations of this interface define custom comparison logic
 * that can be used to sort elements in a SortedLinkedList.
 *
 * @template T
 */
interface ComparatorInterface
{
    /**
     * Compare two values.
     *
     * @param T $a The first value to compare
     * @param T $b The second value to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    public function compare(mixed $a, mixed $b): int;
}