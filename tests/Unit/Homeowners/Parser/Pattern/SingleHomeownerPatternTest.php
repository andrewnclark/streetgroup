<?php

namespace Tests\Unit\Homeowners\Parser\Pattern;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\Pattern\SingleHomeownerPattern;
use App\Homeowners\Person;

class SingleHomeownerPatternTest extends TestCase
{
    private SingleHomeownerPattern $pattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pattern = new SingleHomeownerPattern();
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
            'standard mr' => ['Mr John Smith', true],
            'standard mrs' => ['Mrs Jane Smith', true],
            'full title' => ['Mister John Doe', true],
            'with initial' => ['Mr M Mackie', true],
            'with dot initial' => ['Mr F. Fredrickson', true],
            'with middle initial' => ['Mr John A. Smith', true],
            'hyphenated surname' => ['Mrs Faye Hughes-Eastwood', true],
            'single word' => ['John', false],
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
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Person::class, $result[0]);
        
        $personArray = $result[0]->toArray();
        $this->assertEquals($expected[0], $personArray);
    }

    public static function handleProvider(): array
    {
        return [
            'standard mr' => [
                'Mr John Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'last_name' => 'Smith',
                        'initial' => null
                    ]
                ]
            ],
            'standard mrs' => [
                'Mrs Jane Smith',
                [
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'initial' => null
                    ]
                ]
            ],
            'full title' => [
                'Mister John Doe',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'initial' => null
                    ]
                ]
            ],
            'with initial' => [
                'Mr M Mackie',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'M',
                        'last_name' => 'Mackie',
                        'initial' => null
                    ]
                ]
            ],
            'with dot initial' => [
                'Mr F. Fredrickson',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'F',
                        'last_name' => 'Fredrickson',
                        'initial' => null
                    ]
                ]
            ],
            'with middle initial' => [
                'Mr John A. Smith',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'last_name' => 'Smith',
                        'initial' => 'A'
                    ]
                ]
            ],
            'hyphenated surname' => [
                'Mrs Faye Hughes-Eastwood',
                [
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Faye',
                        'last_name' => 'Hughes-Eastwood',
                        'initial' => null
                    ]
                ]
            ],
            'single word' => ['John', null],
            'empty string' => ['', null],
        ];
    }
}
