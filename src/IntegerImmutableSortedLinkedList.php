<?php

declare(strict_types=1);

namespace SortedLinkedList;

/**
 * Immutable sorted linked list specifically for integers.
 *
 * @extends ImmutableSortedLinkedList<int>
 */
class IntegerImmutableSortedLinkedList extends ImmutableSortedLinkedList
{
    /**
     * Compare two integer values for sorting.
     *
     * @param int $a The first value to compare
     * @param int $b The second value to compare
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    protected function compare(mixed $a, mixed $b): int
    {
        if ($this->comparator !== null) {
            return $this->comparator->compare($a, $b);
        }

        return $a <=> $b;
    }
}
