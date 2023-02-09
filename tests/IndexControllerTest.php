<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Tests;

use PHPUnit\Framework\TestCase;

final class IndexControllerTest extends TestCase
{
    public function test(): void
    {
        self::assertSame('one day i will write a test :^)', strtolower('One day I will write a test :^)'));
    }
}
