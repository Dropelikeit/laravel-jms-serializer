<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Serializer;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class FactoryTest extends TestCase
{
    /**
     * @var MockObject|Config
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function canCreateFactory(): void
    {
        $factory = new Factory();

        $this->assertInstanceOf(Factory::class, $factory);
    }

    /**
     * @test
     */
    public function canBuildSerializer(): void
    {
        $factory = new Factory();

        $this->config->expects($this->once())->method('getCacheDir')->willReturn(__DIR__);
        $this->config->expects($this->once())->method('debug')->willReturn(true);

        $serializer = $factory->getSerializer($this->config);

        $this->assertInstanceOf(SerializerInterface::class, $serializer);
    }
}
