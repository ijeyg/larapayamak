<?php

namespace Ijeyg\Larapayamak\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ijeyg\Larapayamak\Larapayamak
 */
class Larapayamak extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ijeyg\Larapayamak\Larapayamak::class;
    }
}
