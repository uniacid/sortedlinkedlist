<?php

declare(strict_types=1);

namespace SortedLinkedList;

use SortedLinkedList\Comparator\ComparatorInterface;

/**
 * Immutable variant of SortedLinkedList with copy-on-write semantics.
 *
 * All mutating operations return new instances, preserving the original list.
 * Implements structural sharing for efficiency.
 *
 * @template T
 * @extends SortedLinkedList<T>
 */
class ImmutableSortedLinkedList extends SortedLinkedList
{
    /** @var int Maximum number of elements allowed in bulk operations */
    protected const MAX_BULK_SIZE = 100000;
    /**
     * Constructor for creating new instances.
     *
     * @param ComparatorInterface<T>|null $comparator Optional custom comparator
     */
    public function __construct(?ComparatorInterface $comparator = null)
    {
        parent::__construct($comparator);
    }

    /**
     * Create instance with pre-built structure.
     *
     * @param ComparatorInterface<T>|null $comparator Optional custom comparator
     * @param Node<T>|null $head The head node
     * @param int $size The size of the list
     * @return static
     */
    protected static function createWithStructure(
        ?ComparatorInterface $comparator,
        ?Node $head,
        int $size
    ): static {
        /** @var static<T> $instance */
        /** @phpstan-ignore-next-line */
        $instance = new static($comparator);
        $instance->head = $head;
        $instance->size = $size;
        /** @phpstan-ignore-next-line */
        return $instance;
    }

    /**
     * Create a new instance of ImmutableSortedLinkedList.
     *
     * @param ComparatorInterface<T>|null $comparator Optional custom comparator
     * @return static
     */
    public static function create(?ComparatorInterface $comparator = null): static
    {
        /** @phpstan-ignore-next-line */
        return new static($comparator);
    }


    /**
     * Add a value to the list, returning a new instance.
     *
     * @param T $value The value to add
     * @return static A new list with the value added
     */
    public function withAdd(mixed $value): static
    {
        // For simplicity and correctness, clone the entire structure
        // This ensures immutability without complex structural sharing
        $newList = static::createWithStructure($this->comparator, null, 0);

        // Get all values and add the new one
        $values = $this->toArray();
        $values[] = $value;

        // Sort using comparator
        usort($values, [$this, 'compare']);

        // Build new list structure
        // $values is guaranteed to have at least one element (the added value)
        $firstValue = array_shift($values);
        /** @var Node<T> $newHead */
        $newHead = new Node($firstValue);
        $current = $newHead;
        $size = 1;

        foreach ($values as $val) {
            /** @var Node<T> $newNode */
            $newNode = new Node($val);
            $current->setNext($newNode);
            $current = $newNode;
            $size++;
        }

        return static::createWithStructure($this->comparator, $newHead, $size);
    }

    /**
     * Remove a value from the list, returning a new instance.
     *
     * @param T $value The value to remove
     * @return static A new list with the value removed
     */
    public function withRemove(mixed $value): static
    {
        // Get all values
        $values = $this->toArray();

        // Find and remove first occurrence
        $found = false;
        $newValues = [];
        foreach ($values as $val) {
            if (!$found && $this->compare($val, $value) === 0) {
                $found = true;
                continue;
            }
            $newValues[] = $val;
        }

        // If value not found, return new instance with same values
        if (!$found) {
            $newValues = $values;
        }

        // Build new list structure
        if (count($newValues) === 0) {
            return static::createWithStructure($this->comparator, null, 0);
        }

        $firstValue = array_shift($newValues);
        /** @var Node<T> $newHead */
        $newHead = new Node($firstValue);
        $current = $newHead;
        $size = 1;

        foreach ($newValues as $val) {
            /** @var Node<T> $newNode */
            $newNode = new Node($val);
            $current->setNext($newNode);
            $current = $newNode;
            $size++;
        }

        return static::createWithStructure($this->comparator, $newHead, $size);
    }

    /**
     * Clear the list, returning a new empty instance.
     *
     * @return static A new empty list
     */
    public function withClear(): static
    {
        return static::createWithStructure($this->comparator, null, 0);
    }

    /**
     * Add all values from an iterable, returning a new instance.
     *
     * @param iterable<T> $values The values to add
     * @return static A new list with all values added
     */
    public function withAddAll(iterable $values): static
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException(
                sprintf('Cannot add more than %d elements at once', self::MAX_BULK_SIZE)
            );
        }

        if (count($values) === 0) {
            return static::createWithStructure($this->comparator, $this->head, $this->size);
        }

        // For efficiency, convert to array and sort
        $allValues = $this->toArray();
        $allValues = array_merge($allValues, $values);

        // Sort using comparator
        usort($allValues, [$this, 'compare']);

        // Build new list
        // $allValues is guaranteed to have elements (checked above)
        $firstValue = array_shift($allValues);
        /** @var Node<T> $newHead */
        $newHead = new Node($firstValue);
        $current = $newHead;
        $newSize = 1;

        foreach ($allValues as $val) {
            /** @var Node<T> $newNode */
            $newNode = new Node($val);
            $current->setNext($newNode);
            $current = $newNode;
            $newSize++;
        }

        return static::createWithStructure($this->comparator, $newHead, $newSize);
    }

    /**
     * Remove all occurrences of the specified values, returning a new instance.
     *
     * @param iterable<T> $values The values to remove
     * @return static A new list with values removed
     */
    public function withRemoveAll(iterable $values): static
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException(
                sprintf('Cannot remove more than %d elements at once', self::MAX_BULK_SIZE)
            );
        }

        if (count($values) === 0) {
            return static::createWithStructure($this->comparator, $this->head, $this->size);
        }

        // Create a set for O(1) lookups
        $valuesToRemove = array_flip(array_map(
            fn($v) => is_scalar($v)
                ? (string)$v
                : (is_object($v)
                    ? spl_object_id($v)
                    : json_encode($v, JSON_THROW_ON_ERROR)),
            $values
        ));

        /** @var Node<T>|null $newHead */
        $newHead = null;
        /** @var Node<T>|null $newCurrent */
        $newCurrent = null;
        $newSize = 0;

        $current = $this->head;
        while ($current !== null) {
            $value = $current->getValue();
            $currentValueKey = is_scalar($value)
                ? (string)$value
                : (is_object($value)
                    ? spl_object_id($value)
                    : json_encode($value, JSON_THROW_ON_ERROR));

            if (!isset($valuesToRemove[$currentValueKey])) {
                // Keep this node
                /** @var Node<T> $newNode */
                $newNode = new Node($current->getValue());
                if ($newHead === null) {
                    $newHead = $newNode;
                    $newCurrent = $newNode;
                } else {
                    if ($newCurrent !== null) {
                        $newCurrent->setNext($newNode);
                    }
                    $newCurrent = $newNode;
                }
                $newSize++;
            }

            $current = $current->getNext();
        }

        return static::createWithStructure($this->comparator, $newHead, $newSize);
    }

    /**
     * Retain only the values that are in the specified collection, returning a new instance.
     *
     * @param iterable<T> $values The values to retain
     * @return static A new list with only retained values
     */
    public function withRetainAll(iterable $values): static
    {
        if (!is_array($values)) {
            $values = iterator_to_array($values);
        }

        if (count($values) > self::MAX_BULK_SIZE) {
            throw new \InvalidArgumentException(
                sprintf('Cannot retain more than %d elements at once', self::MAX_BULK_SIZE)
            );
        }

        if (count($values) === 0) {
            return static::createWithStructure($this->comparator, null, 0);
        }

        // Create a set for O(1) lookups
        $valuesToRetain = array_flip(array_map(
            fn($v) => is_scalar($v)
                ? (string)$v
                : (is_object($v)
                    ? spl_object_id($v)
                    : json_encode($v, JSON_THROW_ON_ERROR)),
            $values
        ));

        /** @var Node<T>|null $newHead */
        $newHead = null;
        /** @var Node<T>|null $newCurrent */
        $newCurrent = null;
        $newSize = 0;

        $current = $this->head;
        while ($current !== null) {
            $value = $current->getValue();
            $currentValueKey = is_scalar($value)
                ? (string)$value
                : (is_object($value)
                    ? spl_object_id($value)
                    : json_encode($value, JSON_THROW_ON_ERROR));

            if (isset($valuesToRetain[$currentValueKey])) {
                // Keep this node
                /** @var Node<T> $newNode */
                $newNode = new Node($current->getValue());
                if ($newHead === null) {
                    $newHead = $newNode;
                    $newCurrent = $newNode;
                } else {
                    if ($newCurrent !== null) {
                        $newCurrent->setNext($newNode);
                    }
                    $newCurrent = $newNode;
                }
                $newSize++;
            }

            $current = $current->getNext();
        }

        return static::createWithStructure($this->comparator, $newHead, $newSize);
    }

    /**
     * Create a new list with a different comparator, re-sorting all elements.
     *
     * @param ComparatorInterface<T>|null $comparator The new comparator
     * @return static A new list with elements sorted by the new comparator
     */
    public function withComparator(?ComparatorInterface $comparator): static
    {
        if ($this->head === null) {
            return static::createWithStructure($comparator, null, 0);
        }

        // Get all values
        $values = $this->toArray();

        // Create new list with new comparator
        $newList = static::createWithStructure($comparator, null, 0);

        // Sort values with new comparator
        usort($values, [$newList, 'compare']);

        // Build new list structure
        if (count($values) === 0) {
            return $newList;
        }

        $firstValue = array_shift($values);
        /** @var Node<T> $newHead */
        $newHead = new Node($firstValue);
        $current = $newHead;
        $size = 1;

        foreach ($values as $value) {
            /** @var Node<T> $newNode */
            $newNode = new Node($value);
            $current->setNext($newNode);
            $current = $newNode;
            $size++;
        }

        return static::createWithStructure($comparator, $newHead, $size);
    }

    /**
     * Override parent add method to throw exception.
     *
     * @param T $value The value to add
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function add(mixed $value): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withAdd() instead.');
    }

    /**
     * Override parent remove method to throw exception.
     *
     * @param T $value The value to remove
     * @return bool Never returns, always throws exception
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function remove(mixed $value): bool
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withRemove() instead.');
    }

    /**
     * Override parent clear method to throw exception.
     *
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function clear(): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withClear() instead.');
    }

    /**
     * Override parent addAll method to throw exception.
     *
     * @param iterable<T> $values The values to add
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function addAll(iterable $values): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withAddAll() instead.');
    }

    /**
     * Override parent removeAll method to throw exception.
     *
     * @param iterable<T> $values The values to remove
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function removeAll(iterable $values): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withRemoveAll() instead.');
    }

    /**
     * Override parent retainAll method to throw exception.
     *
     * @param iterable<T> $values The values to retain
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function retainAll(iterable $values): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withRetainAll() instead.');
    }

    /**
     * Override parent setComparator to throw exception.
     *
     * @param ComparatorInterface<T>|null $comparator The comparator
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function setComparator(?ComparatorInterface $comparator): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list. Use withComparator() instead.');
    }

    /**
     * Override parent offsetSet to throw exception.
     *
     * @param mixed $offset The offset
     * @param T $value The value
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list through array access.');
    }

    /**
     * Override parent offsetUnset to throw exception.
     *
     * @param mixed $offset The offset
     * @throws \BadMethodCallException Always thrown for immutable list
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('Cannot mutate immutable list through array access.');
    }

    /**
     * Apply a transformation function to each element, returning a new list.
     *
     * @param callable(T): T $callback The transformation function
     * @return static A new list with transformed values
     */
    public function map(callable $callback): static
    {
        $newList = static::createWithStructure($this->comparator, null, 0);

        $current = $this->head;
        $values = [];
        while ($current !== null) {
            $values[] = $callback($current->getValue());
            $current = $current->getNext();
        }

        // Sort the transformed values
        usort($values, [$newList, 'compare']);

        // Build new list
        if (count($values) === 0) {
            return $newList;
        }

        $firstValue = array_shift($values);
        /** @var Node<T> $newHead */
        $newHead = new Node($firstValue);
        $newCurrent = $newHead;
        $size = 1;

        foreach ($values as $value) {
            /** @var Node<T> $newNode */
            $newNode = new Node($value);
            $newCurrent->setNext($newNode);
            $newCurrent = $newNode;
            $size++;
        }

        return static::createWithStructure($this->comparator, $newHead, $size);
    }

    /**
     * Filter elements based on a predicate, returning a new list.
     *
     * @param callable(T): bool $predicate The filter predicate
     * @return static A new list with filtered values
     */
    public function filter(callable $predicate): static
    {
        /** @var Node<T>|null $newHead */
        $newHead = null;
        /** @var Node<T>|null $newCurrent */
        $newCurrent = null;
        $newSize = 0;

        $current = $this->head;
        while ($current !== null) {
            $value = $current->getValue();
            if ($predicate($value)) {
                /** @var Node<T> $newNode */
                $newNode = new Node($value);
                if ($newHead === null) {
                    $newHead = $newNode;
                    $newCurrent = $newNode;
                } else {
                    if ($newCurrent !== null) {
                        $newCurrent->setNext($newNode);
                    }
                    $newCurrent = $newNode;
                }
                $newSize++;
            }
            $current = $current->getNext();
        }

        return static::createWithStructure($this->comparator, $newHead, $newSize);
    }

    /**
     * Create a new ImmutableSortedLinkedList from an array.
     *
     * @param array<T> $values The values to add
     * @param ComparatorInterface<T>|null $comparator Optional comparator
     * @return static
     */
    public static function fromArray(array $values, ?ComparatorInterface $comparator = null): static
    {
        /** @phpstan-ignore-next-line */
        $list = new static($comparator);
        return $list->withAddAll($values);
    }
}
