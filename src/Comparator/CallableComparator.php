<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

/**
 * Comparator that wraps a callable for custom comparison logic.
 *
 * This comparator allows users to provide their own comparison function
 * as a closure, function name, or any callable.
 *
 * @template T
 * @implements ComparatorInterface<T>
 */
class CallableComparator implements ComparatorInterface
{
    /**
     * The callable used for comparison.
     *
     * @var callable(T, T): int
     */
    private $callable;

    /**
     * Create a new callable comparator.
     *
     * @param callable(T, T): int $callable The comparison function
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * Compare two values using the wrapped callable.
     *
     * @param T $a The first value
     * @param T $b The second value
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     */
    public function compare(mixed $a, mixed $b): int
    {
        return ($this->callable)($a, $b);
    }
}