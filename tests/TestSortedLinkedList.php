<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\Node;

/**
 * Concrete implementation for testing the abstract SortedLinkedList class
 */
class TestSortedLinkedList extends SortedLinkedList
{
    /**
     * Compare two values for sorting.
     * This test implementation uses integer comparison.
     */
    protected function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }

    /**
     * Helper method to get the head node for testing
     */
    public function getHead(): ?Node
    {
        return $this->head;
    }
}
