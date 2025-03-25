<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\ParserHelpers;

class AmpersandSeparatedPattern implements PatternInterface
{
    use ParserHelpers;
    
    public function match(string $input): bool
    {
        return str_contains($input, ' & ');
    }
    
    public function handle(string $input): ?array
    {
        $parts = explode(' & ', $input);
        if (count($parts) !== 2) {
            return null;
        }
        
        $firstPart = trim($parts[0]);
        $secondPart = trim($parts[1]);
        
        // Check if first part is just a title
        $firstPartWords = array_filter(explode(' ', $firstPart));
        if (count($firstPartWords) === 1) {
            return $this->handleFirstPartWithTitle($firstPart, $secondPart);
        }
        
        // Both parts have full names
        $firstHomeowner = $this->parseSimpleHomeowner($firstPart);
        $secondHomeowner = $this->parseSimpleHomeowner($secondPart);
        
        if ($firstHomeowner && $secondHomeowner) {
            return [$firstHomeowner, $secondHomeowner];
        }
        
        return null;
    }
    
    private function handleFirstPartWithTitle(string $title, string $secondPart): ?array
    {
        $secondHomeowner = $this->parseSimpleHomeowner($secondPart);
        if (!$secondHomeowner) {
            return null;
        }
        
        $normalizedTitle = $this->normalizeTitle($title);
        $firstHomeowner = $this->createHomeownerArray(
            $normalizedTitle,
            $secondHomeowner['first_name'],
            $secondHomeowner['last_name'],
            $secondHomeowner['initial']
        );
        
        return [$firstHomeowner, $secondHomeowner];
    }
    
    private function parseSimpleHomeowner(string $input): ?array
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
        
        return $this->createHomeownerArray($title, $firstName, $lastName, $initial);
    }
}
