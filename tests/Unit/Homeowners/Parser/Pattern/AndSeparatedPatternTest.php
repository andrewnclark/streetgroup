<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\AndSeparatedPattern;

class AndSeparatedPatternTest extends TestCase
{
    private AndSeparatedPattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new AndSeparatedPattern();
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
            'and separated names' => ['Mr John Smith and Mrs Jane Doe', true],
            'and separated with initials' => ['Mr J. Smith and Dr P. Jones', true],
            'mr and mrs pattern' => ['Mr and Mrs Smith', false], // This is handled by MrAndMrsPattern
            'single person' => ['Mr John Smith', false],
            'ampersand separated' => ['Mr John & Mrs Jane', false],
            'empty string' => ['', false],
        ];
    }

    #[Test]
    #[DataProvider('handleProvider')]
    public function testHandle(string $input, ?array $expected)
    {
        $result = $this->pattern->handle($input);
        $this->assertEquals($expected, $result);
    }

    public static function handleProvider(): array
    {
        return [
            'and separated names' => [
                'Mr John Smith and Mrs Jane Doe',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'last_name' => 'Smith',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                        'initial' => null
                    ]
                ]
            ],
            'and separated with initials' => [
                'Mr J. Smith and Dr P. Jones',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'J',
                        'last_name' => 'Smith',
                        'initial' => null
                    ],
                    [
                        'title' => 'Dr',
                        'first_name' => 'P',
                        'last_name' => 'Jones',
                        'initial' => null
                    ]
                ]
            ],
            'invalid first part' => ['and Mrs Jane Doe', null],
            'invalid second part' => ['Mr John Smith and', null],
            'non-matching input' => ['Mr John Smith', null],
        ];
    }
}
