<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\AmpersandSeparatedPattern;

class AmpersandSeparatedPatternTest extends TestCase
{
    private AmpersandSeparatedPattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new AmpersandSeparatedPattern();
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
            'ampersand with full names' => ['Mr Andrew Clark & Mrs Jessica Clark', true],
            'ampersand with title only first part' => ['Dr & Mrs Joe Bloggs', true],
            'single person' => ['Mr John Smith', false],
            'and separated' => ['Mr John Smith and Mrs Jane Doe', false],
            'mr and mrs pattern' => ['Mr and Mrs Smith', false],
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
            'ampersand with full names' => [
                'Mr Andrew Clark & Mrs Jessica Clark',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'Andrew',
                        'last_name' => 'Clark',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Jessica',
                        'last_name' => 'Clark',
                        'initial' => null
                    ]
                ]
            ],
            'ampersand with title only first part' => [
                'Dr & Mrs Joe Bloggs',
                [
                    [
                        'title' => 'Dr',
                        'first_name' => 'Joe',
                        'last_name' => 'Bloggs',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Joe',
                        'last_name' => 'Bloggs',
                        'initial' => null
                    ]
                ]
            ],
            'ampersand with title only second part' => [
                'Mr Joe Bloggs & Mrs',
                null
            ],
            'invalid first part' => ['& Mrs Jane Doe', null],
            'invalid second part' => ['Mr John Smith &', null],
            'non-matching input' => ['Mr John Smith', null],
        ];
    }
}
