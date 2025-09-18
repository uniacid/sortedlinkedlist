<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\IntegerSortedLinkedList;
use SortedLinkedList\StringSortedLinkedList;
use SortedLinkedList\FloatSortedLinkedList;

class EdgeCaseTest extends TestCase
{
    public function testEmptyListOperations(): void
    {
        $intList = new IntegerSortedLinkedList();
        $stringList = new StringSortedLinkedList();
        $floatList = new FloatSortedLinkedList();

        // Test size on empty lists
        $this->assertEquals(0, $intList->size());
        $this->assertEquals(0, $stringList->size());
        $this->assertEquals(0, $floatList->size());

        // Test contains on empty lists
        $this->assertFalse($intList->contains(0));
        $this->assertFalse($intList->contains(1));
        $this->assertFalse($intList->contains(-1));
        $this->assertFalse($stringList->contains(""));
        $this->assertFalse($stringList->contains("test"));
        $this->assertFalse($floatList->contains(0.0));
        $this->assertFalse($floatList->contains(1.1));

        // Test remove on empty lists
        $this->assertFalse($intList->remove(0));
        $this->assertFalse($stringList->remove("test"));
        $this->assertFalse($floatList->remove(1.0));

        // Test clear on empty lists (should not error)
        $intList->clear();
        $stringList->clear();
        $floatList->clear();

        $this->assertEquals(0, $intList->size());
        $this->assertEquals(0, $stringList->size());
        $this->assertEquals(0, $floatList->size());
    }

    public function testSingleElementOperations(): void
    {
        // Integer list
        $intList = new IntegerSortedLinkedList();
        $intList->add(42);
        $this->assertEquals(1, $intList->size());
        $this->assertTrue($intList->contains(42));
        $this->assertFalse($intList->contains(41));
        $this->assertFalse($intList->contains(43));

        $this->assertTrue($intList->remove(42));
        $this->assertEquals(0, $intList->size());
        $this->assertFalse($intList->contains(42));

        // String list
        $stringList = new StringSortedLinkedList();
        $stringList->add("single");
        $this->assertEquals(1, $stringList->size());
        $this->assertTrue($stringList->contains("single"));
        $this->assertFalse($stringList->contains("other"));

        $stringList->clear();
        $this->assertEquals(0, $stringList->size());

        // Float list
        $floatList = new FloatSortedLinkedList();
        $floatList->add(3.14);
        $this->assertEquals(1, $floatList->size());
        $this->assertTrue($floatList->contains(3.14));
        $this->assertFalse($floatList->contains(3.141));

        $this->assertTrue($floatList->remove(3.14));
        $this->assertEquals(0, $floatList->size());
    }

    public function testDuplicateHandling(): void
    {
        // Integer duplicates
        $intList = new IntegerSortedLinkedList();
        $intList->add(5);
        $intList->add(5);
        $intList->add(5);
        $this->assertEquals(3, $intList->size());

        $this->assertTrue($intList->remove(5));
        $this->assertEquals(2, $intList->size());
        $this->assertTrue($intList->contains(5));

        $intList->remove(5);
        $intList->remove(5);
        $this->assertEquals(0, $intList->size());
        $this->assertFalse($intList->contains(5));

        // String duplicates with exact match
        $stringList = new StringSortedLinkedList();
        $stringList->add("dup");
        $stringList->add("dup");
        $stringList->add("dup");
        $stringList->add("dup");
        $this->assertEquals(4, $stringList->size());

        $removedCount = 0;
        while ($stringList->remove("dup")) {
            $removedCount++;
        }
        $this->assertEquals(4, $removedCount);
        $this->assertEquals(0, $stringList->size());

        // Float duplicates
        $floatList = new FloatSortedLinkedList();
        for ($i = 0; $i < 10; $i++) {
            $floatList->add(1.23);
        }
        $this->assertEquals(10, $floatList->size());
        $this->assertTrue($floatList->contains(1.23));

        // Remove all duplicates one by one
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue($floatList->remove(1.23));
            $this->assertEquals(9 - $i, $floatList->size());
        }
        $this->assertFalse($floatList->remove(1.23));
    }

    public function testBoundaryValues(): void
    {
        // Integer boundaries
        $intList = new IntegerSortedLinkedList();
        $intList->add(PHP_INT_MAX);
        $intList->add(PHP_INT_MIN);
        $intList->add(0);
        $this->assertEquals(3, $intList->size());
        $this->assertTrue($intList->contains(PHP_INT_MAX));
        $this->assertTrue($intList->contains(PHP_INT_MIN));
        $this->assertTrue($intList->contains(0));

        // Float boundaries and special values
        $floatList = new FloatSortedLinkedList();
        $floatList->add(PHP_FLOAT_MAX);
        $floatList->add(PHP_FLOAT_MIN);
        $floatList->add(-PHP_FLOAT_MAX);
        $floatList->add(0.0);
        $floatList->add(-0.0); // Negative zero
        $this->assertEquals(5, $floatList->size());

        // String edge cases
        $stringList = new StringSortedLinkedList();
        $stringList->add("");  // Empty string
        $stringList->add(" "); // Single space
        $stringList->add("\t"); // Tab
        $stringList->add("\n"); // Newline
        $stringList->add("0"); // String zero
        $this->assertEquals(5, $stringList->size());
        $this->assertTrue($stringList->contains(""));
        $this->assertTrue($stringList->contains(" "));
    }

    public function testSpecialCharactersInStrings(): void
    {
        $list = new StringSortedLinkedList();

        // Unicode characters
        $list->add("café");
        $list->add("naïve");
        $list->add("résumé");
        $list->add("🎉");
        $list->add("😀");
        $list->add("中文");
        $list->add("日本語");
        $list->add("한국어");

        $this->assertEquals(8, $list->size());
        $this->assertTrue($list->contains("café"));
        $this->assertTrue($list->contains("🎉"));
        $this->assertTrue($list->contains("中文"));

        // Special characters
        $list->add("!@#$%^&*()");
        $list->add("'quotes'");
        $list->add('"double"');
        $list->add("back\\slash");
        $list->add("forward/slash");

        $this->assertEquals(13, $list->size());
        $this->assertTrue($list->contains("!@#$%^&*()"));
        $this->assertTrue($list->contains("back\\slash"));
    }

    public function testFloatPrecisionEdgeCases(): void
    {
        $list = new FloatSortedLinkedList();

        // Very small differences
        $list->add(0.1 + 0.2); // Should be 0.3 but with float precision issues
        $list->add(0.3);
        $this->assertEquals(2, $list->size());

        // Very small numbers
        $list->add(1e-10);
        $list->add(1e-11);
        $list->add(1e-12);
        $this->assertEquals(5, $list->size());

        // Very large numbers
        $list->add(1e10);
        $list->add(1e11);
        $list->add(1e12);
        $this->assertEquals(8, $list->size());

        // Negative vs positive near zero
        $list->add(0.0000001);
        $list->add(-0.0000001);
        $this->assertEquals(10, $list->size());
    }

    public function testConsecutiveOperations(): void
    {
        $list = new IntegerSortedLinkedList();

        // Add same element multiple times consecutively
        for ($i = 0; $i < 5; $i++) {
            $list->add(10);
        }
        $this->assertEquals(5, $list->size());

        // Remove same element multiple times consecutively
        $removed = 0;
        for ($i = 0; $i < 10; $i++) {
            if ($list->remove(10)) {
                $removed++;
            }
        }
        $this->assertEquals(5, $removed);
        $this->assertEquals(0, $list->size());

        // Multiple clears
        $list->add(1);
        $list->clear();
        $list->clear(); // Should not error
        $list->clear(); // Should not error
        $this->assertEquals(0, $list->size());
    }

    public function testAlternatingMinMax(): void
    {
        $list = new IntegerSortedLinkedList();

        // Alternating between very small and very large values
        $list->add(1);
        $list->add(1000000);
        $list->add(2);
        $list->add(999999);
        $list->add(3);
        $list->add(999998);

        $this->assertEquals(6, $list->size());
        $this->assertTrue($list->contains(1));
        $this->assertTrue($list->contains(1000000));
        $this->assertTrue($list->contains(3));
        $this->assertTrue($list->contains(999998));
    }

    public function testRapidAddRemoveSameElement(): void
    {
        $list = new IntegerSortedLinkedList();

        // Rapidly add and remove the same element
        for ($i = 0; $i < 100; $i++) {
            $list->add(42);
            $this->assertTrue($list->contains(42));
            $this->assertTrue($list->remove(42));
            $this->assertFalse($list->contains(42));
        }

        $this->assertEquals(0, $list->size());
    }

    public function testStringCaseSensitivity(): void
    {
        $list = new StringSortedLinkedList();

        // Add strings with different cases
        $list->add("test");
        $list->add("Test");
        $list->add("TEST");
        $list->add("TeSt");

        $this->assertEquals(4, $list->size());
        $this->assertTrue($list->contains("test"));
        $this->assertTrue($list->contains("Test"));
        $this->assertTrue($list->contains("TEST"));
        $this->assertTrue($list->contains("TeSt"));
        $this->assertFalse($list->contains("tEsT")); // Different case combination
    }

    public function testLongStrings(): void
    {
        $list = new StringSortedLinkedList();

        // Very long strings
        $longString1 = str_repeat("a", 10000);
        $longString2 = str_repeat("b", 10000);
        $longString3 = str_repeat("a", 9999) . "b"; // Slightly different

        $list->add($longString1);
        $list->add($longString2);
        $list->add($longString3);

        $this->assertEquals(3, $list->size());
        $this->assertTrue($list->contains($longString1));
        $this->assertTrue($list->contains($longString2));
        $this->assertTrue($list->contains($longString3));

        $this->assertTrue($list->remove($longString3));
        $this->assertEquals(2, $list->size());
    }

    public function testNegativeNumbers(): void
    {
        // Integer negatives
        $intList = new IntegerSortedLinkedList();
        $intList->add(-5);
        $intList->add(-10);
        $intList->add(0);
        $intList->add(5);
        $intList->add(-1);

        $this->assertEquals(5, $intList->size());
        $this->assertTrue($intList->contains(-10));
        $this->assertTrue($intList->contains(-5));
        $this->assertTrue($intList->contains(-1));

        // Float negatives
        $floatList = new FloatSortedLinkedList();
        $floatList->add(-3.14);
        $floatList->add(-2.71);
        $floatList->add(-0.01);
        $floatList->add(0.01);
        $floatList->add(1.41);

        $this->assertEquals(5, $floatList->size());
        $this->assertTrue($floatList->contains(-3.14));
        $this->assertTrue($floatList->contains(0.01));
    }
}