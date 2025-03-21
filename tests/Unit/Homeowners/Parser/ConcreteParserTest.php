<?php

namespace Tests\Unit\Homeowners\Parser;

use PHPUnit\Framework\TestCase;
use App\Homeowners\Parser\ConcreteParser;

class ConcreteParserTest extends TestCase
{
    private ConcreteParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ConcreteParser();
    }

    /**
     * @dataProvider singleHomeownerProvider
     */
    public function testParseSingleHomeowner(string $input, array $expected)
    {
        $result = $this->parser->parse($input);
        $this->assertEquals([$expected], $result);
    }

    public function singleHomeownerProvider(): array
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

    /**
     * @dataProvider multipleHomeownersProvider
     */
    public function testParseMultipleHomeowners(string $input, array $expected)
    {
        $result = $this->parser->parse($input);
        $this->assertEquals($expected, $result);
    }

    public function multipleHomeownersProvider(): array
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
                        'first_name' => 'Smith',
                        'last_name' => 'Smith',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Smith',
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
                        'first_name' => 'Jones',
                        'last_name' => 'Jones',
                        'initial' => null
                    ],
                    [
                        'title' => 'Mrs',
                        'first_name' => 'Jones',
                        'last_name' => 'Jones',
                        'initial' => null
                    ]
                ]
            ]
        ];
    }
}
