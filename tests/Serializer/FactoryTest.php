<?php

namespace Dropelikeit\LaravelJmsSerializer\Tests\Serializer;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\Tests\Serializer\data\CustomHandler;
use InvalidArgumentException;
use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class FactoryTest extends TestCase
{
    /**
     * @test
     */
    public function canCreateSerializerWithNoDefaultsOrCustomHandlers(): void
    {
        $serializer = (new Factory())->getSerializer(Config::fromConfig([
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => false,
            'custom_handlers' => [],
        ]));

        $this->assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * @test
     */
    public function canCreateSerializerWithoutCustomHandlers(): void
    {
        $serializer = (new Factory())->getSerializer(Config::fromConfig([
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [],
        ]));

        $this->assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * @test
     */
    public function canCreateSerializerWithValidCustomHandlers(): void
    {
        $serializer = (new Factory())->getSerializer(Config::fromConfig([
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                CustomHandler::class,
            ],
        ]));

        $this->assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * @test
     */
    public function canCreateSerializerWithCustomHandlerAsObject(): void
    {
        $handler = $this->createMock(CustomHandlerConfiguration::class);
        $handler
            ->expects(self::once())
            ->method('getDirection')
            ->willReturn(1);

        $handler
            ->expects(self::once())
            ->method('getTypeName')
            ->willReturn('myObject');

        $handler
            ->expects(self::once())
            ->method('getFormat')
            ->willReturn('json');

        $handler
            ->expects(self::once())
            ->method('getCallable')
            ->willReturn(
                static function (JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context) {
                    return 'hello world!';
                }
            );

        $serializer = (new Factory())->getSerializer(Config::fromConfig([
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                $handler,
            ],
        ]));

        $this->assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * @test
     */
    public function canNotCreateSerializerWithInvalidCustomHandler(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Factory())->getSerializer(Config::fromConfig([
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                new class() {},
            ],
        ]));
    }
}
