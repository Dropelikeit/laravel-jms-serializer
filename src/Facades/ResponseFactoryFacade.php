<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Facades;

use Illuminate\Support\Facades\Facade;

final class ResponseFactoryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ResponseFactory';
    }
}
