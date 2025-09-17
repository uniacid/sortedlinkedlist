<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\Node;

/**
 * @covers \SortedLinkedList\Node
 */
class NodeTest extends TestCase
{
    public function testConstructorInitializesWithValue(): void
    {
        $node = new Node(42);
        $this->assertSame(42, $node->getValue());
        $this->assertNull($node->getNext());
    }

    public function testConstructorInitializesWithValueAndNext(): void
    {
        $nextNode = new Node(100);
        $node = new Node(42, $nextNode);

        $this->assertSame(42, $node->getValue());
        $this->assertSame($nextNode, $node->getNext());
    }

    public function testSetAndGetValue(): void
    {
        $node = new Node('initial');
        $this->assertSame('initial', $node->getValue());

        $node->setValue('updated');
        $this->assertSame('updated', $node->getValue());
    }

    public function testSetAndGetNext(): void
    {
        $node = new Node('first');
        $this->assertNull($node->getNext());

        $nextNode = new Node('second');
        $node->setNext($nextNode);
        $this->assertSame($nextNode, $node->getNext());

        $node->setNext(null);
        $this->assertNull($node->getNext());
    }

    public function testChainedNodes(): void
    {
        $third = new Node('third');
        $second = new Node('second', $third);
        $first = new Node('first', $second);

        $this->assertSame('first', $first->getValue());
        $this->assertSame('second', $first->getNext()?->getValue());
        $this->assertSame('third', $first->getNext()?->getNext()?->getValue());
        $this->assertNull($first->getNext()?->getNext()?->getNext());
    }

    public function testNodeWithIntegerValues(): void
    {
        $node1 = new Node(10);
        $node2 = new Node(20);
        $node1->setNext($node2);

        $this->assertSame(10, $node1->getValue());
        $this->assertSame(20, $node1->getNext()?->getValue());
    }

    public function testNodeWithStringValues(): void
    {
        $node1 = new Node('apple');
        $node2 = new Node('banana');
        $node1->setNext($node2);

        $this->assertSame('apple', $node1->getValue());
        $this->assertSame('banana', $node1->getNext()?->getValue());
    }

    public function testNodeWithFloatValues(): void
    {
        $node1 = new Node(3.14);
        $node2 = new Node(2.71);
        $node1->setNext($node2);

        $this->assertSame(3.14, $node1->getValue());
        $this->assertSame(2.71, $node1->getNext()?->getValue());
    }

    public function testNodeWithBooleanValues(): void
    {
        $node1 = new Node(true);
        $node2 = new Node(false);
        $node1->setNext($node2);

        $this->assertTrue($node1->getValue());
        $this->assertFalse($node1->getNext()?->getValue());
    }

    public function testNodeWithArrayValues(): void
    {
        $array1 = ['a' => 1, 'b' => 2];
        $array2 = ['c' => 3, 'd' => 4];

        $node1 = new Node($array1);
        $node2 = new Node($array2);
        $node1->setNext($node2);

        $this->assertSame($array1, $node1->getValue());
        $this->assertSame($array2, $node1->getNext()?->getValue());
    }

    public function testNodeWithObjectValues(): void
    {
        $obj1 = new \stdClass();
        $obj1->property = 'value1';

        $obj2 = new \stdClass();
        $obj2->property = 'value2';

        $node1 = new Node($obj1);
        $node2 = new Node($obj2);
        $node1->setNext($node2);

        $this->assertSame($obj1, $node1->getValue());
        $this->assertSame($obj2, $node1->getNext()?->getValue());
    }

    public function testNodeUpdateInChain(): void
    {
        $node3 = new Node(3);
        $node2 = new Node(2, $node3);
        $node1 = new Node(1, $node2);

        // Update middle node's value
        $node2->setValue(22);
        $this->assertSame(22, $node1->getNext()?->getValue());

        // Update middle node's next pointer
        $node4 = new Node(4);
        $node2->setNext($node4);
        $this->assertSame(4, $node1->getNext()?->getNext()?->getValue());
    }

    public function testNodeSelfReference(): void
    {
        $node = new Node('self');
        $node->setNext($node);

        $this->assertSame($node, $node->getNext());
        $this->assertSame('self', $node->getNext()?->getValue());
    }

    public function testCompareToWithIntegers(): void
    {
        $node1 = new Node(10);
        $node2 = new Node(20);
        $node3 = new Node(10);

        $this->assertLessThan(0, $node1->compareTo(20));
        $this->assertGreaterThan(0, $node2->compareTo(10));
        $this->assertSame(0, $node1->compareTo(10));
        $this->assertSame(0, $node3->compareTo(10));
    }

    public function testCompareToWithStrings(): void
    {
        $node1 = new Node('apple');
        $node2 = new Node('banana');
        $node3 = new Node('apple');

        $this->assertLessThan(0, $node1->compareTo('banana'));
        $this->assertGreaterThan(0, $node2->compareTo('apple'));
        $this->assertSame(0, $node1->compareTo('apple'));
        $this->assertSame(0, $node3->compareTo('apple'));
    }

    public function testCompareToWithFloats(): void
    {
        $node1 = new Node(1.5);
        $node2 = new Node(2.7);
        $node3 = new Node(1.5);

        $this->assertLessThan(0, $node1->compareTo(2.7));
        $this->assertGreaterThan(0, $node2->compareTo(1.5));
        $this->assertSame(0, $node1->compareTo(1.5));
        $this->assertSame(0, $node3->compareTo(1.5));
    }

    public function testCompareToWithBooleans(): void
    {
        $nodeTrue = new Node(true);
        $nodeFalse = new Node(false);

        $this->assertGreaterThan(0, $nodeTrue->compareTo(false));
        $this->assertLessThan(0, $nodeFalse->compareTo(true));
        $this->assertSame(0, $nodeTrue->compareTo(true));
        $this->assertSame(0, $nodeFalse->compareTo(false));
    }

    public function testCompareToWithMixedTypes(): void
    {
        // Test with arrays (uses fallback comparison)
        $node1 = new Node(['a' => 1]);

        // Arrays are compared element by element
        $result = $node1->compareTo(['b' => 2]);
        $this->assertIsInt($result);

        // Test with objects (uses fallback comparison)
        $obj1 = new \stdClass();
        $obj1->value = 1;
        $obj2 = new \stdClass();
        $obj2->value = 2;

        $nodeObj = new Node($obj1);
        $resultObj = $nodeObj->compareTo($obj2);
        $this->assertIsInt($resultObj);
    }
}