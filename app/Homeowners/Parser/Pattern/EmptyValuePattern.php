<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Person;

class EmptyValuePattern implements PatternInterface
{
    public function match(string $input): bool
    {
        return trim($input) === '';
    }
    
    public function handle(string $input): ?array
    {
        return [];
    }
}
