<?php

namespace App\Homeowners\Parser;

use App\Homeowners\Person;

trait ParserHelpers
{
    private array $titleMap = [
        'mr' => 'Mr',
        'mister' => 'Mr',
        'mrs' => 'Mrs',
        'ms' => 'Ms',
        'miss' => 'Miss',
        'dr' => 'Dr',
        'doctor' => 'Dr',
        'prof' => 'Prof',
        'professor' => 'Prof'
    ];

    /**
     * Create a Person object with the given homeowner details
     */
    protected function createPerson(string $title, ?string $firstName, string $lastName, ?string $initial = null): Person
    {
        return new Person(
            $title,
            $firstName,
            $lastName,
            $initial
        );
    }

    /**
     * @deprecated Use createPerson() instead
     */
    protected function createHomeownerArray(string $title, ?string $firstName, string $lastName, ?string $initial = null): array
    {
        return [
            'title' => $title,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'initial' => $initial
        ];
    }

    protected function normalizeTitle(string $title): string
    {
        $key = strtolower(trim($title));
        return $this->titleMap[$key] ?? ucfirst($key);
    }
}
