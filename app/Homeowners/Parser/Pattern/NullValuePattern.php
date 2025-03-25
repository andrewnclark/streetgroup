<?php

namespace App\Homeowners\Parser\Pattern;

use App\Homeowners\Parser\Contract\PatternInterface;

class NullValuePattern implements PatternInterface
{
    public function match(string $input): bool
    {
        return $input === 'null' || $input === 'NULL';
    }
    
    public function handle(string $input): ?array
    {
        return [];
    }
}
