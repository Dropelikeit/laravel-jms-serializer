<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Config;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class ConfigTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProviderCanCreateConfig
     * @param array $config
     */
    public function canCreateConfig(array $config)
    {
        $configTest = Config::fromConfig($config);

        $this->assertEquals($config['serialize_null'], $configTest->shouldSerializeNull());
        $this->assertEquals(sprintf('%s%s', $config['cache_dir'], '/serializer/'), $configTest->getCacheDir());
        $this->assertEquals($config['serialize_type'], $configTest->getSerializeType());
        $this->assertEquals($config['debug'], $configTest->debug());
    }

    public function dataProviderCanCreateConfig(): array
    {
        return [
            'success' => [
                [
                    'serialize_null' => false,
                    'cache_dir' => '/storage',
                    'serialize_type' => 'json',
                    'debug' => false,
                ],
            ],
        ];
    }
}
