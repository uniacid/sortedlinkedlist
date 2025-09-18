<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use InvalidArgumentException;

/**
 * Comparator for string values with optional case sensitivity.
 *
 * This comparator can perform case-sensitive or case-insensitive
 * string comparisons based on configuration.
 *
 * @implements ComparatorInterface<string>
 */
class StringComparator implements ComparatorInterface
{
    /**
     * Whether the comparison should be case-sensitive.
     *
     * @var bool
     */
    private bool $caseSensitive;

    /**
     * Create a new string comparator.
     *
     * @param bool $caseSensitive Whether to perform case-sensitive comparison (default: true)
     */
    public function __construct(bool $caseSensitive = true)
    {
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * Compare two string values.
     *
     * @param string $a The first string value
     * @param string $b The second string value
     * @return int Negative if $a < $b, positive if $a > $b, zero if equal
     * @throws InvalidArgumentException If either value is not a string
     */
    public function compare(mixed $a, mixed $b): int
    {
        // Runtime validation for type safety
        // @phpstan-ignore-next-line
        if (!is_string($a) || !is_string($b)) {
            throw new InvalidArgumentException('StringComparator can only compare string values');
        }

        if ($this->caseSensitive) {
            return strcmp($a, $b);
        } else {
            return strcasecmp($a, $b);
        }
    }
}