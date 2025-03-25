<?php

namespace App\Homeowners\Parser\Contract;

use App\Homeowners\Person;

interface PatternInterface
{
    /**
     * Check if the input string matches this pattern
     */
    public function match(string $input): bool;
    
    /**
     * Handle the input string and transform it into homeowner data
     * 
     * @return Person[]|null An array of Person objects or null if the input cannot be handled
     */
    public function handle(string $input): ?array;
}
