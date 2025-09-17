<?php

declare(strict_types=1);

namespace SortedLinkedList;

/**
 * Represents a single node in a linked list structure.
 *
 * @template T
 */
class Node
{
    /**
     * The value stored in this node.
     *
     * @var T
     */
    private mixed $value;

    /**
     * Reference to the next node in the list.
     *
     * @var Node<T>|null
     */
    private ?Node $next;

    /**
     * Creates a new node with the specified value.
     *
     * @param T $value The value to store in the node
     * @param Node<T>|null $next Optional reference to the next node
     */
    public function __construct(mixed $value, ?Node $next = null)
    {
        $this->value = $value;
        $this->next = $next;
    }

    /**
     * Gets the value stored in this node.
     *
     * @return T The stored value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Sets the value for this node.
     *
     * @param T $value The new value to store
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Gets the reference to the next node.
     *
     * @return Node<T>|null The next node or null if this is the last node
     */
    public function getNext(): ?Node
    {
        return $this->next;
    }

    /**
     * Sets the reference to the next node.
     *
     * @param Node<T>|null $next The next node or null to make this the last node
     */
    public function setNext(?Node $next): void
    {
        $this->next = $next;
    }

    /**
     * Compares this node's value with another value for sorting purposes.
     *
     * @param T $other The value to compare against
     * @return int Negative if this value is less than other,
     *             positive if greater, zero if equal
     */
    public function compareTo(mixed $other): int
    {
        // Handle numeric comparison (integers and floats)
        if (is_numeric($this->value) && is_numeric($other)) {
            return $this->value <=> $other;
        }

        // Handle string comparison
        if (is_string($this->value) && is_string($other)) {
            return strcmp($this->value, $other);
        }

        // Handle boolean comparison
        if (is_bool($this->value) && is_bool($other)) {
            return ((int) $this->value) <=> ((int) $other);
        }

        // For other types, use spaceship operator
        return $this->value <=> $other;
    }
}