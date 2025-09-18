<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

/**
 * Factory class for creating common comparator instances.
 *
 * This class provides convenient static methods for creating
 * commonly used comparators.
 *
 * @package SortedLinkedList\Comparator
 */
class ComparatorFactory
{
    /**
     * Create a numeric comparator for integers and floats.
     *
     * @return NumericComparator
     */
    public static function numeric(): NumericComparator
    {
        return new NumericComparator();
    }

    /**
     * Create a string comparator with optional case sensitivity.
     *
     * @param bool $caseSensitive Whether to perform case-sensitive comparison (default: true)
     * @return StringComparator
     */
    public static function string(bool $caseSensitive = true): StringComparator
    {
        return new StringComparator($caseSensitive);
    }

    /**
     * Create a date comparator for DateTime objects.
     *
     * @return DateComparator
     */
    public static function date(): DateComparator
    {
        return new DateComparator();
    }

    /**
     * Create a comparator that reverses another comparator's order.
     *
     * @template T
     * @param ComparatorInterface<T> $comparator The comparator to reverse
     * @return ReverseComparator<T>
     */
    public static function reverse(ComparatorInterface $comparator): ReverseComparator
    {
        return new ReverseComparator($comparator);
    }

    /**
     * Create a comparator from a callable.
     *
     * @template T
     * @param callable(T, T): int $callable The comparison function
     * @return CallableComparator<T>
     */
    public static function callable(callable $callable): CallableComparator
    {
        return new CallableComparator($callable);
    }
}