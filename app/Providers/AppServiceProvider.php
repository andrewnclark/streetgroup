<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use App\Homeowners\Homeowner;
use App\Homeowners\Parser\Contract\ParserInterface;
use App\Homeowners\Parser\ConcreteParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

        // $this->app->register(Homeowner::class, function (Application $app) {
        //     $implementation = $app->make(ConcreteParser::class);
        //     return new Homeowner($implementation);
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
