<?php

namespace App\Homeowners\Parser;

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

    protected function createHomeownerArray(string $title, string $firstName, string $lastName, ?string $initial = null): array
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
