<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\ParserHelpers;

class SingleHomeownerPattern implements PatternInterface
{
    use ParserHelpers;
    
    public function match(string $input): bool
    {
        // This is a fallback pattern that matches any input with at least two words
        $parts = array_filter(explode(' ', trim($input)));
        return count($parts) >= 2;
    }
    
    public function handle(string $input): ?array
    {
        $parts = array_values(array_filter(explode(' ', trim($input))));
        
        // We no longer need to check for empty input since EmptyValuePattern will handle it
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
        
        return [$this->createHomeownerArray($title, $firstName, $lastName, $initial)];
    }
}
