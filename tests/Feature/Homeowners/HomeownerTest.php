<?php

namespace Tests\Feature\Homeowners;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Homeowners\Homeowner;
use App\Homeowners\Person;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HomeownerTest extends TestCase
{
    /**
     * Test that the Homeowner service returns arrays, not Person objects
     */
    #[Test]
    #[DataProvider('singleHomeownerProvider')]
    public function testParseSingleHomeowner(string $input, array $expected)
    {
        // Resolve the Homeowner service from the container
        $homeowner = $this->app->make(Homeowner::class);
        
        $result = $homeowner->parseHomeownerString($input);
        
        // Verify that the result is an array
        $this->assertIsArray($result);
        
        // Verify that the array contains exactly one item
        $this->assertCount(1, $result);
        
        // Verify that the result item is an array, not a Person object
        $this->assertIsArray($result[0]);
        $this->assertNotInstanceOf(Person::class, $result[0]);
        
        // Verify the array has the expected values
        $this->assertEquals($expected[0], $result[0]);
    }

    public static function singleHomeownerProvider(): array
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
            ]
        ];
    }

    /**
     * Test that the Homeowner service properly handles multiple homeowners
     */
    #[Test]
    #[DataProvider('multipleHomeownersProvider')]
    public function testParseMultipleHomeowners(string $input, array $expected)
    {
        // Resolve the Homeowner service from the container
        $homeowner = $this->app->make(Homeowner::class);
        
        $result = $homeowner->parseHomeownerString($input);
        
        // Verify that the result is an array with the expected number of items
        $this->assertIsArray($result);
        $this->assertCount(count($expected), $result);
        
        // Verify that each item in the array is an array, not a Person object
        foreach ($result as $index => $item) {
            $this->assertIsArray($item);
            $this->assertNotInstanceOf(Person::class, $item);
            $this->assertEquals($expected[$index], $item);
        }
    }

    public static function multipleHomeownersProvider(): array
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
            'and separated' => [
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
            'ampersand separated' => [
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
            ]
        ];
    }

    /**
     * Test that the Homeowner service properly handles empty input
     */
    #[Test]
    public function testEmptyInput()
    {
        // Resolve the Homeowner service from the container
        $homeowner = $this->app->make(Homeowner::class);
        
        $result = $homeowner->parseHomeownerString('');
        
        // Verify that the result is an empty array
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that the Homeowner service properly handles null input
     */
    #[Test]
    public function testNullInput()
    {
        // Resolve the Homeowner service from the container
        $homeowner = $this->app->make(Homeowner::class);
        
        $result = $homeowner->parseHomeownerString('null');
        
        // Verify that the result is an empty array
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
