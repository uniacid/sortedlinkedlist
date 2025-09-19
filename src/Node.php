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
}
