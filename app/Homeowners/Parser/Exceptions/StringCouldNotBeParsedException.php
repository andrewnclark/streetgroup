<?php

namespace App\Homeowners\Parser\Exceptions;

use Exception;

class StringCouldNotBeParsedException extends Exception
{
    public function __construct(string $message = "The provided string could not be parsed", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}