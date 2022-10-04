<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Config;

use function array_diff;
use function array_keys;
use Dropelikeit\LaravelJmsSerializer\Contracts\Config as ResponseBuilderConfig;

use Dropelikeit\LaravelJmsSerializer\Exception\MissingRequiredItems;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use function implode;
use function in_array;
use function sprintf;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Config implements ResponseBuilderConfig
{
    /**
     * @psalm-var non-empty-string
     */
    private string $cacheDir;

    private bool $shouldSerializeNull;

    /**
     * @var string 'json'|'xml'
     */
    private string $serializeType;

    private bool $debug;

    /**
     * @psalm-param ResponseBuilderConfig::SERIALIZE_TYPE_* $serializeType
     */
    private function __construct(string $cacheDir, bool $shouldSerializeNull, string $serializeType, bool $debug)
    {
        $cacheDir = sprintf('%s%s', $cacheDir, self::CACHE_DIR);
        Assert::stringNotEmpty($cacheDir);

        $this->cacheDir = $cacheDir;
        $this->shouldSerializeNull = $shouldSerializeNull;
        $this->serializeType = $serializeType;
        $this->debug = $debug;
    }

    /**
     * @param array<string, bool|string> $config
     * @psalm-param array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool} $config
     *
     * @return self
     */
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
        ], true)) {
            throw SerializeType::fromUnsupportedSerializeType($config['serialize_type']);
        }

        return new self(
            $config['cache_dir'],
            (bool) $config['serialize_null'],
            $config['serialize_type'],
            (bool) $config['debug']
        );
    }

    /**
     * @psalm-return non-empty-string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    public function shouldSerializeNull(): bool
    {
        return $this->shouldSerializeNull;
    }

    /**
     * @return string 'json'|'xml'
     */
    public function getSerializeType(): string
    {
        return $this->serializeType;
    }

    public function debug(): bool
    {
        return $this->debug;
    }
}
