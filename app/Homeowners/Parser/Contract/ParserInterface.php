<?php

namespace App\Homeowners\Parser\Contract;

use App\Homeowners\Person;

interface ParserInterface
{
    /**
     * Parse a string into an array of Person objects
     * 
     * @param string $string The input string to parse
     * @return Person[] An array of Person objects
     */
    public function parse(string $string): array;
}
