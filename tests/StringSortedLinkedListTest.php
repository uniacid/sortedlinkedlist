<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\StringSortedLinkedList;
use InvalidArgumentException;

class StringSortedLinkedListTest extends TestCase
{
    private StringSortedLinkedList $list;

    protected function setUp(): void
    {
        $this->list = new StringSortedLinkedList();
    }

    public function testEmptyListHasSizeZero(): void
    {
        $this->assertEquals(0, $this->list->size());
    }

    public function testAddSingleString(): void
    {
        $this->list->add("hello");
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains("hello"));
    }

    public function testAddMultipleStringsInOrder(): void
    {
        $this->list->add("apple");
        $this->list->add("banana");
        $this->list->add("cherry");

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains("apple"));
        $this->assertTrue($this->list->contains("banana"));
        $this->assertTrue($this->list->contains("cherry"));
    }

    public function testAddMultipleStringsOutOfOrder(): void
    {
        $this->list->add("zebra");
        $this->list->add("apple");
        $this->list->add("middle");

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains("zebra"));
        $this->assertTrue($this->list->contains("apple"));
        $this->assertTrue($this->list->contains("middle"));
    }

    public function testAddDuplicateStrings(): void
    {
        $this->list->add("test");
        $this->list->add("test");
        $this->list->add("test");

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains("test"));
    }

    public function testAddEmptyString(): void
    {
        $this->list->add("");
        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains(""));
    }

    public function testAddStringsWithSpaces(): void
    {
        $this->list->add("hello world");
        $this->list->add("  leading spaces");
        $this->list->add("trailing spaces  ");
        $this->list->add("  both sides  ");

        $this->assertEquals(4, $this->list->size());
        $this->assertTrue($this->list->contains("hello world"));
        $this->assertTrue($this->list->contains("  leading spaces"));
        $this->assertTrue($this->list->contains("trailing spaces  "));
        $this->assertTrue($this->list->contains("  both sides  "));
    }

    public function testAddSpecialCharacters(): void
    {
        $this->list->add("!@#$%^&*()");
        $this->list->add("hello-world");
        $this->list->add("test_123");
        $this->list->add("file.txt");
        $this->list->add("[brackets]");
        $this->list->add("{braces}");

        $this->assertEquals(6, $this->list->size());
        $this->assertTrue($this->list->contains("!@#$%^&*()"));
        $this->assertTrue($this->list->contains("hello-world"));
        $this->assertTrue($this->list->contains("test_123"));
        $this->assertTrue($this->list->contains("file.txt"));
        $this->assertTrue($this->list->contains("[brackets]"));
        $this->assertTrue($this->list->contains("{braces}"));
    }

    public function testAddUnicodeCharacters(): void
    {
        $this->list->add("Hello 世界");
        $this->list->add("émoji 😀");
        $this->list->add("Γειά σου");
        $this->list->add("مرحبا");
        $this->list->add("Привет");

        $this->assertEquals(5, $this->list->size());
        $this->assertTrue($this->list->contains("Hello 世界"));
        $this->assertTrue($this->list->contains("émoji 😀"));
        $this->assertTrue($this->list->contains("Γειά σου"));
        $this->assertTrue($this->list->contains("مرحبا"));
        $this->assertTrue($this->list->contains("Привет"));
    }

    public function testCaseSensitiveComparison(): void
    {
        $this->list->add("Apple");
        $this->list->add("apple");
        $this->list->add("APPLE");

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains("Apple"));
        $this->assertTrue($this->list->contains("apple"));
        $this->assertTrue($this->list->contains("APPLE"));

        // Verify case-sensitive contains
        $this->assertFalse($this->list->contains("aPpLe"));
    }

    public function testAddNumericStrings(): void
    {
        $this->list->add("1");
        $this->list->add("10");
        $this->list->add("100");
        $this->list->add("2");
        $this->list->add("20");

        $this->assertEquals(5, $this->list->size());
        $this->assertTrue($this->list->contains("1"));
        $this->assertTrue($this->list->contains("10"));
        $this->assertTrue($this->list->contains("100"));
        $this->assertTrue($this->list->contains("2"));
        $this->assertTrue($this->list->contains("20"));
    }

    public function testRemoveFromEmptyList(): void
    {
        $this->assertFalse($this->list->remove("test"));
        $this->assertEquals(0, $this->list->size());
    }

    public function testRemoveExistingString(): void
    {
        $this->list->add("alpha");
        $this->list->add("beta");
        $this->list->add("gamma");

        $this->assertTrue($this->list->remove("beta"));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains("beta"));
        $this->assertTrue($this->list->contains("alpha"));
        $this->assertTrue($this->list->contains("gamma"));
    }

    public function testRemoveFirstString(): void
    {
        $this->list->add("alpha");
        $this->list->add("beta");
        $this->list->add("gamma");

        $this->assertTrue($this->list->remove("alpha"));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains("alpha"));
    }

    public function testRemoveLastString(): void
    {
        $this->list->add("alpha");
        $this->list->add("beta");
        $this->list->add("gamma");

        $this->assertTrue($this->list->remove("gamma"));
        $this->assertEquals(2, $this->list->size());
        $this->assertFalse($this->list->contains("gamma"));
    }

    public function testRemoveNonExistentString(): void
    {
        $this->list->add("alpha");
        $this->list->add("beta");

        $this->assertFalse($this->list->remove("delta"));
        $this->assertEquals(2, $this->list->size());
    }

    public function testRemoveOnlyString(): void
    {
        $this->list->add("lonely");

        $this->assertTrue($this->list->remove("lonely"));
        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains("lonely"));
    }

    public function testRemoveEmptyString(): void
    {
        $this->list->add("");
        $this->list->add("not empty");

        $this->assertTrue($this->list->remove(""));
        $this->assertEquals(1, $this->list->size());
        $this->assertFalse($this->list->contains(""));
        $this->assertTrue($this->list->contains("not empty"));
    }

    public function testRemoveFirstOccurrenceOfDuplicate(): void
    {
        $this->list->add("duplicate");
        $this->list->add("duplicate");
        $this->list->add("duplicate");

        $this->assertTrue($this->list->remove("duplicate"));
        $this->assertEquals(2, $this->list->size());
        $this->assertTrue($this->list->contains("duplicate"));
    }

    public function testContainsOnEmptyList(): void
    {
        $this->assertFalse($this->list->contains("test"));
    }

    public function testContainsExistingString(): void
    {
        $this->list->add("hello");
        $this->list->add("world");

        $this->assertTrue($this->list->contains("hello"));
        $this->assertTrue($this->list->contains("world"));
    }

    public function testContainsNonExistentString(): void
    {
        $this->list->add("hello");
        $this->list->add("world");

        $this->assertFalse($this->list->contains("goodbye"));
    }

    public function testClearEmptyList(): void
    {
        $this->list->clear();
        $this->assertEquals(0, $this->list->size());
    }

    public function testClearNonEmptyList(): void
    {
        $this->list->add("one");
        $this->list->add("two");
        $this->list->add("three");

        $this->list->clear();

        $this->assertEquals(0, $this->list->size());
        $this->assertFalse($this->list->contains("one"));
        $this->assertFalse($this->list->contains("two"));
        $this->assertFalse($this->list->contains("three"));
    }

    public function testAddAfterClear(): void
    {
        $this->list->add("initial");
        $this->list->clear();
        $this->list->add("new");

        $this->assertEquals(1, $this->list->size());
        $this->assertTrue($this->list->contains("new"));
        $this->assertFalse($this->list->contains("initial"));
    }

    public function testAddIntegerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        $this->list->add(123);
    }

    public function testAddFloatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        $this->list->add(3.14);
    }

    public function testAddBooleanThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        $this->list->add(true);
    }

    public function testAddNullThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        $this->list->add(null);
    }

    public function testAddArrayThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        $this->list->add(['array', 'values']);
    }

    public function testLongStringHandling(): void
    {
        $longString1 = str_repeat('a', 10000);
        $longString2 = str_repeat('b', 10000);
        $longString3 = str_repeat('c', 10000);

        $this->list->add($longString2);
        $this->list->add($longString1);
        $this->list->add($longString3);

        $this->assertEquals(3, $this->list->size());
        $this->assertTrue($this->list->contains($longString1));
        $this->assertTrue($this->list->contains($longString2));
        $this->assertTrue($this->list->contains($longString3));
    }

    public function testLargeDataset(): void
    {
        $words = [];
        for ($i = 0; $i < 1000; $i++) {
            $words[] = "word_" . str_pad((string)$i, 4, '0', STR_PAD_LEFT);
        }

        shuffle($words);

        foreach ($words as $word) {
            $this->list->add($word);
        }

        $this->assertEquals(1000, $this->list->size());

        // Verify all words are present
        foreach ($words as $word) {
            $this->assertTrue($this->list->contains($word), "Word $word should be in the list");
        }
    }

    public function testStressTestWithRandomOperations(): void
    {
        $operations = 5000;
        $values = [];
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        for ($i = 0; $i < $operations; $i++) {
            // Generate random string
            $length = rand(1, 20);
            $randomString = '';
            for ($j = 0; $j < $length; $j++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            if (rand(0, 1) === 0) {
                // Add operation
                $this->list->add($randomString);
                $values[] = $randomString;
            } else {
                // Remove operation
                if (!empty($values)) {
                    $indexToRemove = array_rand($values);
                    $valueToRemove = $values[$indexToRemove];
                    if ($this->list->remove($valueToRemove)) {
                        unset($values[$indexToRemove]);
                        $values = array_values($values);
                    }
                }
            }
        }

        $this->assertEquals(count($values), $this->list->size());
    }

    public function testAddWithIntegerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        // @phpstan-ignore-next-line
        $this->list->add(123);
    }

    public function testAddWithFloatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a string');

        // @phpstan-ignore-next-line
        $this->list->add(3.14);
    }

    public function testRemoveWithWrongTypeReturnsFalse(): void
    {
        $this->list->add("apple");
        $this->list->add("banana");
        $this->list->add("cherry");

        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(123));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(3.14));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(null));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->remove(true));

        $this->assertEquals(3, $this->list->size());
    }

    public function testContainsWithWrongTypeReturnsFalse(): void
    {
        $this->list->add("apple");
        $this->list->add("banana");
        $this->list->add("cherry");

        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(123));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(3.14));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(null));
        // @phpstan-ignore-next-line
        $this->assertFalse($this->list->contains(false));

        $this->assertTrue($this->list->contains("apple"));
        $this->assertTrue($this->list->contains("banana"));
    }
}
