<?php

namespace Tests\Unit\Homeowners\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Homeowners\Parser\ArrayParser;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use App\Homeowners\Person;

class ArrayParserTest extends TestCase
{
    private ArrayParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ArrayParser();
    }

    #[Test]
    #[DataProvider('singleHomeownerProvider')]
    public function testParseSingleHomeowner(string $input, array $expected)
    {
        // Parse the input string using the ArrayParser
        $result = $this->parser->parse($input);
        
        // Verify that the result is an array
        $this->assertIsArray($result);
        
        // Verify that the array contains exactly one item
        $this->assertCount(1, $result);
        
        // Verify that the array contains a Person object
        $this->assertInstanceOf(Person::class, $result[0]);
        
        // Verify the Person object has the expected values
        $this->assertEquals($expected, $result[0]->toArray());
    }

    public static function singleHomeownerProvider(): array
    {
        return [
            'standard mr' => [
                'Mr John Smith',
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'initial' => null
                ]
            ],
            'standard mrs' => [
                'Mrs Jane Smith',
                [
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'initial' => null
                ]
            ],
            'full title' => [
                'Mister John Doe',
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'initial' => null
                ]
            ],
            'single initial' => [
                'Mr M Mackie',
                [
                    'title' => 'Mr',
                    'first_name' => 'M',
                    'last_name' => 'Mackie',
                    'initial' => null
                ]
            ],
            'mc name' => [
                'Mrs Jane McMaster',
                [
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'last_name' => 'McMaster',
                    'initial' => null
                ]
            ],
            'doctor with initial' => [
                'Dr P Gunn',
                [
                    'title' => 'Dr',
                    'first_name' => 'P',
                    'last_name' => 'Gunn',
                    'initial' => null
                ]
            ],
            'ms title' => [
                'Ms Claire Robbo',
                [
                    'title' => 'Ms',
                    'first_name' => 'Claire',
                    'last_name' => 'Robbo',
                    'initial' => null
                ]
            ],
            'professor' => [
                'Prof Alex Brogan',
                [
                    'title' => 'Prof',
                    'first_name' => 'Alex',
                    'last_name' => 'Brogan',
                    'initial' => null
                ]
            ],
            'hyphenated surname' => [
                'Mrs Faye Hughes-Eastwood',
                [
                    'title' => 'Mrs',
                    'first_name' => 'Faye',
                    'last_name' => 'Hughes-Eastwood',
                    'initial' => null
                ]
            ],
            'initial with dot' => [
                'Mr F. Fredrickson',
                [
                    'title' => 'Mr',
                    'first_name' => 'F',
                    'last_name' => 'Fredrickson',
                    'initial' => null
                ]
            ]
        ];
    }

    #[Test]
    #[DataProvider('multipleHomeownersProvider')]
    public function testParseMultipleHomeowners(string $input, array $expected)
    {
        // Parse the input string using the ArrayParser
        $result = $this->parser->parse($input);
        
        // Verify that the result is an array
        $this->assertIsArray($result);
        
        // Verify that the array contains the expected number of items
        $this->assertCount(count($expected), $result);
        
        // Verify that each item in the array is a Person object with the expected values
        foreach ($result as $index => $person) {
            $this->assertInstanceOf(Person::class, $person);
            $this->assertEquals($expected[$index], $person->toArray());
        }
    }

    public static function multipleHomeownersProvider(): array
    {
        return [
            'and separator' => [
                'Mr Tom Staff and Mr John Doe',
                [
                    [
                        'title' => 'Mr',
                        'first_name' => 'Tom',
                        'last_name' => 'Staff',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mr',
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'initial' => null
                    ]
                ]
            ],
            'ampersand with shared name' => [
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
            'ampersand with different names' => [
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
            'mr and mrs pattern' => [
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
            'dr and mrs pattern' => [
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
            ]
        ];
    }
    
    #[Test]
    #[DataProvider('unparsableInputProvider')]
    public function testUnparsableInput(string $input, string $expectedExceptionMessage)
    {
        $this->expectException(StringCouldNotBeParsedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        
        $this->parser->parse($input);
    }
    
    public static function unparsableInputProvider(): array
    {
        return [
            'empty string' => ['', "Cannot parse empty value"],
            'null string' => ['null', "Cannot parse null value"],
            'invalid format' => ['InvalidFormat', "No pattern found to parse: 'InvalidFormat'"],
        ];
    }
}
