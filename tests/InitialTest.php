<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Initial test to verify PHPUnit setup
 */
class InitialTest extends TestCase
{
    public function testPhpUnitSetup(): void
    {
        $configValue = 1;
        self::assertSame(1, $configValue, 'PHPUnit is properly configured');
    }
}