<?php

namespace App\Homeowners\Parser\Contract;

interface PatternInterface
{
    /**
     * Check if the input string matches this pattern
     */
    public function match(string $input): bool;
    
    /**
     * Handle the input string and transform it into homeowner data
     */
    public function handle(string $input): ?array;
}
