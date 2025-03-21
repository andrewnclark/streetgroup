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

    function parseHomeownerString(string $string)
    {
        $return = $this->parser->parse($string);

        return $return;
    }
}