<?php

namespace Tests\Unit\Homeowners;

use App\Homeowners\Person;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class PersonTest extends TestCase
{
    #[Test]
    public function testConstructorAndGetters()
    {
        $person = new Person('Mr', 'John', 'Smith', 'A');
        
        $this->assertEquals('Mr', $person->getTitle());
        $this->assertEquals('John', $person->getFirstName());
        $this->assertEquals('Smith', $person->getLastName());
        $this->assertEquals('A', $person->getInitial());
    }
    
    #[Test]
    public function testConstructorWithNullValues()
    {
        $person = new Person('Mrs', null, 'Jones');
        
        $this->assertEquals('Mrs', $person->getTitle());
        $this->assertNull($person->getFirstName());
        $this->assertEquals('Jones', $person->getLastName());
        $this->assertNull($person->getInitial());
    }
    
    #[Test]
    #[DataProvider('toArrayProvider')]
    public function testToArray(string $title, ?string $firstName, string $lastName, ?string $initial, array $expected)
    {
        $person = new Person($title, $firstName, $lastName, $initial);
        $this->assertEquals($expected, $person->toArray());
    }
    
    public static function toArrayProvider(): array
    {
        return [
            'complete person' => [
                'Mr', 'John', 'Smith', 'A',
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'initial' => 'A'
                ]
            ],
            'without initial' => [
                'Mrs', 'Jane', 'Doe', null,
                [
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'initial' => null
                ]
            ],
            'without first name' => [
                'Dr', null, 'Jones', null,
                [
                    'title' => 'Dr',
                    'first_name' => null,
                    'last_name' => 'Jones',
                    'initial' => null
                ]
            ]
        ];
    }
    
    #[Test]
    #[DataProvider('fromArrayProvider')]
    public function testFromArray(array $data, string $expectedTitle, ?string $expectedFirstName, string $expectedLastName, ?string $expectedInitial)
    {
        $person = Person::fromArray($data);
        
        $this->assertEquals($expectedTitle, $person->getTitle());
        $this->assertEquals($expectedFirstName, $person->getFirstName());
        $this->assertEquals($expectedLastName, $person->getLastName());
        $this->assertEquals($expectedInitial, $person->getInitial());
    }
    
    public static function fromArrayProvider(): array
    {
        return [
            'complete person' => [
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'initial' => 'A'
                ],
                'Mr', 'John', 'Smith', 'A'
            ],
            'without initial' => [
                [
                    'title' => 'Mrs',
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'initial' => null
                ],
                'Mrs', 'Jane', 'Doe', null
            ],
            'without first name' => [
                [
                    'title' => 'Dr',
                    'first_name' => null,
                    'last_name' => 'Jones',
                    'initial' => null
                ],
                'Dr', null, 'Jones', null
            ],
            'missing optional keys' => [
                [
                    'title' => 'Ms',
                    'last_name' => 'Williams'
                ],
                'Ms', null, 'Williams', null
            ]
        ];
    }
}
