<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\EmptyValuePattern;
use App\Homeowners\Person;

class EmptyValuePatternTest extends TestCase
{
    private EmptyValuePattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new EmptyValuePattern();
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
            'empty string' => ['', true],
            'whitespace only' => [' ', true],
            'tabs and newlines' => ["\t\n", true],
            'null string' => ['null', false],
            'text' => ['Mr John Smith', false],
        ];
    }

    #[Test]
    #[DataProvider('handleProvider')]
    public function testHandle(string $input, array $expected)
    {
        $result = $this->pattern->handle($input);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
        $this->assertEquals($expected, $result);
    }

    public static function handleProvider(): array
    {
        return [
            'empty string' => ['', []],
            'whitespace only' => [' ', []],
            'tabs and newlines' => ["\t\n", []],
        ];
    }
}
