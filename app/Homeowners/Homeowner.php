<?php

namespace App\Homeowners;

use App\Homeowners\Parser\Contract\ParserInterface;

class Homeowner
{
    private $parser;

    function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parse a homeowner string into an array of homeowner arrays
     * 
     * @param string $string The input string to parse
     * @return array An array of homeowner arrays, each with title, first_name, last_name, and initial keys
     */
    function parseHomeownerString(string $string): array
    {
        $persons = $this->parser->parse($string);
        
        // Convert Person objects to arrays
        return array_map(function($person) {
            return $person->toArray();
        }, $persons);
    }
}