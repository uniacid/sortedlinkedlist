<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

/**
 * Decorator comparator that reverses the order of another comparator.
 *
 * This comparator wraps another comparator and inverts its comparison result,
 * effectively reversing the sort order.
 *
 * @template T
 * @implements ComparatorInterface<T>
 */
class ReverseComparator implements ComparatorInterface
{
    /**
     * The underlying comparator to reverse.
     *
     * @var ComparatorInterface<T>
     */
    private ComparatorInterface $baseComparator;

    /**
     * Create a new reverse comparator.
     *
     * @param ComparatorInterface<T> $baseComparator The comparator to reverse
     */
    public function __construct(ComparatorInterface $baseComparator)
    {
        $this->baseComparator = $baseComparator;
    }

    /**
     * Compare two values in reverse order.
     *
     * @param T $a The first value
     * @param T $b The second value
     * @return int Negative if $a > $b, positive if $a < $b, zero if equal
     */
    public function compare(mixed $a, mixed $b): int
    {
        // Invert the result of the base comparator
        return -$this->baseComparator->compare($a, $b);
    }
}
