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

    public const KEY_SERIALIZE_NULL = 'serialize_null';
    public const KEY_CACHE_DIR = 'cache_dir';
    public const KEY_SERIALIZE_TYPE = 'serialize_type';
    public const KEY_DEBUG = 'debug';
    public const KEY_ADD_DEFAULT_HANDLERS = 'add_default_handlers';
    public const KEY_CUSTOM_HANDLERS = 'custom_handlers';

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
