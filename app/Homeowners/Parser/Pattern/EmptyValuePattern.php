<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use App\Homeowners\Person;

class EmptyValuePattern implements PatternInterface
{
    public function match(string $input): bool
    {
        return trim($input) === '';
    }
    
    /**
     * @throws StringCouldNotBeParsedException
     */
    public function handle(string $input): ?array
    {
        throw new StringCouldNotBeParsedException("Cannot parse empty value");
    }
}
