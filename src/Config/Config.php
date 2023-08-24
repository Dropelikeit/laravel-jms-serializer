<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Config;

use function array_diff;
use function array_keys;
use Dropelikeit\LaravelJmsSerializer\Contracts\Config as ResponseBuilderConfig;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;

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
    private readonly string $cacheDir;

    /**
     * @var array<int, CustomHandlerConfiguration|class-string>
     */
    private readonly array $customHandlers;

    /**
     * @param array<int, CustomHandlerConfiguration|class-string> $customHandlers
     */
    private function __construct(
        private readonly bool $shouldSerializeNull,
        /**
         * @psalm-var ResponseBuilderConfig::SERIALIZE_TYPE_*
         */
        private readonly string $serializeType,
        private readonly bool $debug,
        private readonly bool $addDefaultHandlers,
        string $cacheDir,
        array $customHandlers,
    ) {
        $cacheDir = sprintf('%s%s', $cacheDir, self::CACHE_DIR);
        Assert::stringNotEmpty($cacheDir);

        $this->cacheDir = $cacheDir;
        $this->customHandlers = $customHandlers;
    }

    /**
     * @param array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config
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
            'add_default_handlers',
            'custom_handlers',
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
            cacheDir: $config['cache_dir'],
            customHandlers: (array) $config['custom_handlers'],
            shouldSerializeNull: (bool) $config['serialize_null'],
            serializeType: $config['serialize_type'],
            debug: (bool) $config['debug'],
            addDefaultHandlers: (bool) $config['add_default_handlers'],
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

    public function shouldAddDefaultHeaders(): bool
    {
        return $this->addDefaultHandlers;
    }

    /**
     * @return array<int, CustomHandlerConfiguration|class-string>
     */
    public function getCustomHandlers(): array
    {
        return $this->customHandlers;
    }
}
