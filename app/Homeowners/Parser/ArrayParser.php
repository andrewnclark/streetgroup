<?php

namespace App\Homeowners\Parser;

use App\Homeowners\Parser\Contract\ParserInterface;
use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\Pattern\MrAndMrsPattern;
use App\Homeowners\Parser\Pattern\AndSeparatedPattern;
use App\Homeowners\Parser\Pattern\AmpersandSeparatedPattern;
use App\Homeowners\Parser\Pattern\SingleHomeownerPattern;
use App\Homeowners\Parser\Pattern\NullValuePattern;
use App\Homeowners\Parser\Pattern\EmptyValuePattern;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use App\Homeowners\Person;

class ArrayParser implements ParserInterface
{
    /**
     * @var PatternInterface[]
     */
    private array $patterns;

    public function __construct()
    {
        // Order matters - more specific patterns should be checked first
        $this->patterns = [
            new EmptyValuePattern(),
            new NullValuePattern(),
            new MrAndMrsPattern(),
            new AndSeparatedPattern(),
            new AmpersandSeparatedPattern(),
            new SingleHomeownerPattern(),
        ];
    }

    /**
     * Parse a string into an array of Person objects
     * 
     * @param string $string The input string to parse
     * @return Person[] An array of Person objects
     * @throws StringCouldNotBeParsedException If the string cannot be parsed
     */
    public function parse(string $string): array
    {
        $string = trim($string);

        $parser = collect($this->patterns)->first(function ($pattern) use ($string) {
            return $pattern->match($string);
        });

        if ($parser === null) {
            throw new StringCouldNotBeParsedException("No pattern found to parse: '$string'");
        }

        $result = $parser->handle($string);
        if ($result !== null) {
            return $result;
        }

        throw new StringCouldNotBeParsedException("Failed to parse: '$string'");
    }
}
