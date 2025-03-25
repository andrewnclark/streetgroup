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

class ConcreteParser implements ParserInterface
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

    public function parse(string $string): array
    {
        $string = trim($string);

        $parser = collect($this->patterns)->first(function ($pattern) use ($string) {
            return $pattern->match($string);
        });

        if ($parser === null) {
            return [];
        }

        $result = $parser->handle($string);
        if ($result !== null) {
            return $result;
        }

        return [];
    }
}