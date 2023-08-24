<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Config;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use Dropelikeit\LaravelJmsSerializer\Exception\MissingRequiredItems;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ConfigTest extends TestCase
{
    /**
     *
     * @test
     * @dataProvider dataProviderCanCreateConfig
     * @param array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config
     * @param bool $throwMissingException
     * @param bool $throwWrongTypeException
     */
    public function canCreateConfig(array $config, bool $throwMissingException, bool $throwWrongTypeException): void
    {
        if ($throwMissingException) {
            $this->expectException(MissingRequiredItems::class);
        }

        if ($throwWrongTypeException) {
            $this->expectException(SerializeType::class);
        }

        $configTest = Config::fromConfig($config);

        self::assertEquals($config['serialize_null'], $configTest->shouldSerializeNull());
        self::assertEquals(sprintf('%s%s', $config['cache_dir'], '/serializer/'), $configTest->getCacheDir());
        self::assertEquals($config['serialize_type'], $configTest->getSerializeType());
        self::assertEquals($config['debug'], $configTest->debug());
        self::assertEquals(true, $configTest->shouldAddDefaultHeaders());
        self::assertCount(0, $configTest->getCustomHandlers());
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function dataProviderCanCreateConfig(): array
    {
        return [
            'success' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                false,
                false,
            ],
            'missing_required_fields' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                true,
                false,
            ],
            'wrong_serialize_type' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                false,
                true,
            ],
        ];
    }
}
