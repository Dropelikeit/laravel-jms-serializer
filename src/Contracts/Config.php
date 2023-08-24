<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Contracts;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface Config
{
    public const SERIALIZE_TYPE_JSON = 'json';
    public const SERIALIZE_TYPE_XML = 'xml';
    public const CACHE_DIR = '/serializer/';

    public function getCacheDir(): string;

    public function shouldSerializeNull(): bool;

    public function getSerializeType(): string;

    public function debug(): bool;

    public function shouldAddDefaultHeaders(): bool;

    /**
     * @return array<int, CustomHandlerConfiguration|class-string>
     */
    public function getCustomHandlers(): array;
}
