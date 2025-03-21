<?php

namespace App\Homeowners\Parser;

use App\Homeowners\Parser\Contract\ParserInterface;

class ConcreteParser implements ParserInterface
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

    public function parse(string $string): array
    {
        $string = trim($string);
        
        if ($result = $this->parseMrAndMrsPattern($string)) {
            return $result;
        }
        
        if ($result = $this->parseAndSeparatedNames($string)) {
            return $result;
        }
        
        if ($result = $this->parseAmpersandSeparatedNames($string)) {
            return $result;
        }
        
        return $this->parseHomeowner($string);
    }
    
    private function parseMrAndMrsPattern(string $string): ?array
    {
        if (preg_match('/^(Mr|Mrs|Dr|Ms|Prof)\s+and\s+(Mr|Mrs|Dr|Ms|Prof)\s+([A-Za-z-]+)$/i', $string, $matches)) {
            $lastName = $matches[3];
            return [
                $this->createHomeownerArray($this->normalizeTitle($matches[1]), $lastName, $lastName),
                $this->createHomeownerArray($this->normalizeTitle($matches[2]), $lastName, $lastName)
            ];
        }
        
        return null;
    }
    
    private function parseAndSeparatedNames(string $string): ?array
    {
        if (str_contains($string, ' and ')) {
            $parts = explode(' and ', $string);
            return array_merge(
                $this->parseHomeowner($parts[0]),
                $this->parseHomeowner($parts[1])
            );
        }
        
        return null;
    }
    
    private function parseAmpersandSeparatedNames(string $string): ?array
    {
        if (!str_contains($string, ' & ')) {
            return null;
        }
        
        $parts = explode(' & ', $string);
        
        if ($result = $this->handleSecondPartWithFullName($parts)) {
            return $result;
        }
        
        return $this->handleFirstPartWithFullName($parts);
    }
    
    private function handleSecondPartWithFullName(array $parts): ?array
    {
        $secondPerson = $this->parseHomeowner($parts[1]);
        
        if (empty($secondPerson)) {
            return null;
        }
        
        $secondPersonData = $secondPerson[0];
        
        // Check if first part is just a title
        if (count(array_filter(explode(' ', trim($parts[0])))) === 1) {
            return [
                $this->createHomeownerArray(
                    $this->normalizeTitle($parts[0]),
                    $secondPersonData['first_name'],
                    $secondPersonData['last_name']
                ),
                $secondPersonData
            ];
        }
        
        // Try to parse first part normally
        $firstPerson = $this->parseHomeowner($parts[0]);
        if (!empty($firstPerson)) {
            return array_merge($firstPerson, $secondPerson);
        }
        
        return null;
    }
    
    private function handleFirstPartWithFullName(array $parts): ?array
    {
        $firstPerson = $this->parseHomeowner($parts[0]);
        if (!empty($firstPerson)) {
            $firstPersonData = $firstPerson[0];
            return [
                $firstPersonData,
                $this->createHomeownerArray(
                    $this->normalizeTitle($parts[1]),
                    $firstPersonData['first_name'],
                    $firstPersonData['last_name']
                )
            ];
        }
        
        return null;
    }
    
    private function parseHomeowner(string $string): array
    {
        $parts = array_values(array_filter(explode(' ', trim($string))));
        
        if (count($parts) < 2) {
            return [];
        }
        
        $title = $this->normalizeTitle($parts[0]);
        $firstName = $parts[1];
        $lastName = end($parts);
        $initial = null;
        
        // Handle first name initial with dot
        if (str_ends_with($firstName, '.')) {
            $firstName = rtrim($firstName, '.');
        }
        
        // Check for middle initial
        if (isset($parts[2]) && str_contains($parts[2], '.')) {
            $initial = trim($parts[2], '.');
            $lastName = $parts[3] ?? $lastName;
        }
        
        return [
            $this->createHomeownerArray($title, $firstName, $lastName, $initial)
        ];
    }
    
    private function createHomeownerArray(string $title, string $firstName, string $lastName, ?string $initial = null): array
    {
        return [
            'title' => $title,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'initial' => $initial
        ];
    }
    
    private function normalizeTitle(string $title): string
    {
        $key = strtolower(trim($title));
        return $this->titleMap[$key] ?? ucfirst($key);
    }
}