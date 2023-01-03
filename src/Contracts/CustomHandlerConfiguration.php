<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Contracts;

interface CustomHandlerConfiguration
{
    public function getDirection(): int;

    /**
     * @psalm-return non-empty-string
     */
    public function getTypeName(): string;

    /**
     * @psalm-return non-empty-string
     */
    public function getFormat(): string;

    public function getCallable(): callable;
}
