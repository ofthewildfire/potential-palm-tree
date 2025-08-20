<?php

namespace Fuascailtdev\FilamentResourceBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fuascailtdev\FilamentResourceBuilder\FilamentResourceBuilder
 */
class FilamentResourceBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fuascailtdev\FilamentResourceBuilder\FilamentResourceBuilder::class;
    }
}
