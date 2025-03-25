<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use App\Homeowners\Person;

class NullValuePattern implements PatternInterface
{
    public function match(string $input): bool
    {
        return $input === 'null' || $input === 'NULL';
    }
    
    /**
     * @throws StringCouldNotBeParsedException
     */
    public function handle(string $input): ?array
    {
        throw new StringCouldNotBeParsedException("Cannot parse null value");
    }
}
