<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\ParserHelpers;
use App\Homeowners\Person;

class AndSeparatedPattern implements PatternInterface
{
    use ParserHelpers;
    
    public function match(string $input): bool
    {
        return str_contains($input, ' and ') && !preg_match('/^(Mr|Mrs|Dr|Ms|Prof)\s+and\s+(Mr|Mrs|Dr|Ms|Prof)\s+([A-Za-z-]+)$/i', $input);
    }
    
    public function handle(string $input): ?array
    {
        $parts = explode(' and ', $input);
        
        // Check if we have the required parts
        if (count($parts) < 2) {
            return null;
        }
        
        $firstPart = trim($parts[0] ?? '');
        $secondPart = trim($parts[1] ?? '');
        
        // Check if either part is empty after trimming
        if ($firstPart === '' || $secondPart === '') {
            return null;
        }
        
        $firstHomeowner = $this->parseSimpleHomeowner($firstPart);
        $secondHomeowner = $this->parseSimpleHomeowner($secondPart);
        
        if ($firstHomeowner && $secondHomeowner) {
            return [$firstHomeowner, $secondHomeowner];
        }
        
        return null;
    }
    
    private function parseSimpleHomeowner(string $input): ?Person
    {
        $parts = array_values(array_filter(explode(' ', trim($input))));
        if (count($parts) < 2) {
            return null;
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
        
        return $this->createPerson($title, $firstName, $lastName, $initial);
    }
}
