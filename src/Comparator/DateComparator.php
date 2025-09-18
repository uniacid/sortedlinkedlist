<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use InvalidArgumentException;
use DateTimeInterface;

/**
 * Comparator for DateTime objects.
 *
 * This comparator compares DateTime and DateTimeImmutable objects
 * based on their timestamp values.
 *
 * @implements ComparatorInterface<DateTimeInterface>
 */
class DateComparator implements ComparatorInterface
{
    /**
     * Compare two DateTime objects.
     *
     * @param DateTimeInterface $a The first date
     * @param DateTimeInterface $b The second date
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     * @throws InvalidArgumentException If either value is not a DateTime object
     */
    public function compare(mixed $a, mixed $b): int
    {
        // Runtime validation for type safety
        // @phpstan-ignore-next-line
        if (!($a instanceof DateTimeInterface) || !($b instanceof DateTimeInterface)) {
            throw new InvalidArgumentException('DateComparator can only compare DateTime objects');
        }

        return $a->getTimestamp() <=> $b->getTimestamp();
    }
}