<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Config;

use Dropelikeit\LaravelJmsSerializer\Exception\MissingRequiredItems;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class Config
{
    /**
     * @var string
     */
    public const SERIALIZE_TYPE_JSON = 'json';

    /**
     * @var string
     */
    public const SERIALIZE_TYPE_XML = 'xml';

    /**
     * @var string
     */
    private const CACHE_DIR = '/serializer/';

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $shouldSerializeNull = false;

    /**
     * @var string
     */
    private $serializeType;

    /**
     * @var bool
     */
    private $debug;

    private function __construct(string $cacheDir, bool $shouldSerializeNull, string $serializeType, bool $debug)
    {
        $this->cacheDir = sprintf('%s%s', $cacheDir, self::CACHE_DIR);
        $this->shouldSerializeNull = $shouldSerializeNull;
        $this->serializeType = $serializeType;
        $this->debug = $debug;
    }

    public static function fromConfig(array $config): self
    {
        $missing = array_diff([
            'serialize_null',
            'cache_dir',
            'serialize_type',
            'debug',
        ], array_keys($config));

        if (!empty($missing)) {
            throw MissingRequiredItems::fromConfig(implode(',', $missing));
        }

        if (!in_array($config['serialize_type'], [
            self::SERIALIZE_TYPE_JSON,
            self::SERIALIZE_TYPE_XML,
        ])) {
            throw SerializeType::fromUnsupportedSerializeType($config['serialize_type']);
        }

        return new self($config['cache_dir'], $config['serialize_null'], $config['serialize_type'], $config['debug']);
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function shouldSerializeNull(): bool
    {
        return $this->shouldSerializeNull;
    }

    public function getSerializeType(): string
    {
        return $this->serializeType;
    }

    public function debug(): bool
    {
        return $this->debug;
    }
}
