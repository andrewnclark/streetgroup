<?php

namespace App\Homeowners\Parser;

use Illuminate\Support\ServiceProvider;
use App\Homeowners\Parser\Contract\ParserInterface;
use App\Homeowners\Parser\ConcreteParser;


class ParserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ParserInterface::class, ConcreteParser::class);
    }
}