<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Config;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use Dropelikeit\LaravelJmsSerializer\Exception\MissingRequiredItems;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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
            $this->expectExceptionCode(400);
        }

        if ($throwWrongTypeException) {
            $this->expectException(SerializeType::class);
        }

        $configTest = Config::fromConfig($config);

        self::assertEquals($config['serialize_null'], $configTest->shouldSerializeNull());
        self::assertEquals(sprintf('%s%s', $config['cache_dir'], '/serializer/'), $configTest->getCacheDir());
        self::assertEquals($config['serialize_type'], $configTest->getSerializeType());
        self::assertEquals($config['debug'], $configTest->debug());
        self::assertTrue($configTest->shouldAddDefaultHeaders());
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
            'missing_required_serialize_null_key' => [
                [
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                true,
                false,
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProviderCanNotCreateConfigBecauseInvalidArgumentExceptionThrows')]
    public function canNotCreateConfigBecauseInvalidArgumentExceptionThrows(array $config, int $statusCode, string $expectedError): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode($statusCode);
        $this->expectExceptionMessage($expectedError);

        Config::fromConfig($config);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function dataProviderCanNotCreateConfigBecauseInvalidArgumentExceptionThrows(): array
    {
        return [
            'serialize_null_not_a_boolean' => [
                [
                    'serialize_null' => 1,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'debug_is_not_a_boolean' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'debug' => 2,
                    'serialize_type' => 'json',
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'add_default_handler_is_not_a_boolean' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                    'add_default_handlers' => 2,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a boolean. Got: integer',
            ],
            'serialize_type_is_not_json_or_xml' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'yaml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                400,
                'Unknown given type "yaml" allowed types are "json" and "xml"',
            ],
            'cache_dir_is_not_a_string' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => 123,
                    'serialize_type' => 'xml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => [],
                ],
                0,
                'Expected a string. Got: integer',
            ],
            'custom_handlers' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'xml',
                    'debug' => false,
                    'add_default_handlers' => true,
                    'custom_handlers' => '',
                ],
                0,
                'Expected an array. Got: string',
            ],
        ];
    }
}
