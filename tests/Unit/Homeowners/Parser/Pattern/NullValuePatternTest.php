<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\NullValuePattern;

class NullValuePatternTest extends TestCase
{
    private NullValuePattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new NullValuePattern();
    }

    #[Test]
    #[DataProvider('matchProvider')]
    public function testMatch(string $input, bool $expected)
    {
        $result = $this->pattern->match($input);
        $this->assertSame($expected, $result);
    }

    public static function matchProvider(): array
    {
        return [
            'lowercase null' => ['null', true],
            'uppercase NULL' => ['NULL', true],
            'mixed case Null' => ['Null', false],
            'empty string' => ['', false],
            'whitespace' => [' ', false],
            'other text' => ['Mr John Smith', false],
        ];
    }

    #[Test]
    #[DataProvider('handleProvider')]
    public function testHandle(string $input, array $expected)
    {
        $result = $this->pattern->handle($input);
        $this->assertEquals($expected, $result);
    }

    public static function handleProvider(): array
    {
        return [
            'null value' => ['null', []],
            'NULL value' => ['NULL', []],
        ];
    }
}
