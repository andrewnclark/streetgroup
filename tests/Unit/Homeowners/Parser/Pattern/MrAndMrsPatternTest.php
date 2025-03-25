<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\MrAndMrsPattern;
use App\Homeowners\Person;

class MrAndMrsPatternTest extends TestCase
{
    private MrAndMrsPattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new MrAndMrsPattern();
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
            'mr and mrs' => ['Mr and Mrs Smith', true],
            'dr and mrs' => ['Dr and Mrs Jones', true],
            'mr and ms' => ['Mr and Ms Johnson', true],
            'prof and dr' => ['Prof and Dr Williams', true],
            'single person' => ['Mr John Smith', false],
            'and separated' => ['Mr John Smith and Mrs Jane Doe', false],
            'ampersand separated' => ['Mr John & Mrs Jane', false],
            'empty string' => ['', false],
        ];
    }

    #[Test]
    #[DataProvider('handleProvider')]
    public function testHandle(string $input, ?array $expected)
    {
        $result = $this->pattern->handle($input);
        
        if ($expected === null) {
            $this->assertNull($result);
            return;
        }
        
        $this->assertIsArray($result);
        $this->assertCount(count($expected), $result);
        
        foreach ($result as $index => $person) {
            $this->assertInstanceOf(Person::class, $person);
            $this->assertEquals($expected[$index], $person->toArray());
        }
    }

    public static function handleProvider(): array
    {
        return [
            'mr and mrs' => [
                'Mr and Mrs Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'last_name' => 'Smith',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => null,
                        'last_name' => 'Smith',
                        'initial' => null
                    ]
                ]
            ],
            'dr and mrs' => [
                'Dr and Mrs Jones',
                [
                    [
                        'title' => 'Dr',
                        'first_name' => null,
                        'last_name' => 'Jones',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => null,
                        'last_name' => 'Jones',
                        'initial' => null
                    ]
                ]
            ],
            'case insensitive' => [
                'mr and mrs Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => null,
                        'last_name' => 'Smith',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => null,
                        'last_name' => 'Smith',
                        'initial' => null
                    ]
                ]
            ],
            'non-matching input' => ['Mr John Smith', null],
        ];
    }
}
