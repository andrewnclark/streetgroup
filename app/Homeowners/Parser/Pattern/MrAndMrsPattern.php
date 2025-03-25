<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\ParserHelpers;
use App\Homeowners\Person;

class MrAndMrsPattern implements PatternInterface
{
    use ParserHelpers;
    
    public function match(string $input): bool
    {
        return (bool) preg_match('/^(Mr|Mrs|Dr|Ms|Prof)\s+and\s+(Mr|Mrs|Dr|Ms|Prof)\s+([A-Za-z-]+)$/i', $input);
    }
    
    public function handle(string $input): ?array
    {
        if (preg_match('/^(Mr|Mrs|Dr|Ms|Prof)\s+and\s+(Mr|Mrs|Dr|Ms|Prof)\s+([A-Za-z-]+)$/i', $input, $matches)) {
            $lastName = $matches[3];
            return [
                $this->createPerson($this->normalizeTitle($matches[1]), null, $lastName),
                $this->createPerson($this->normalizeTitle($matches[2]), null, $lastName)
            ];
        }
        
        return null;
    }
}
