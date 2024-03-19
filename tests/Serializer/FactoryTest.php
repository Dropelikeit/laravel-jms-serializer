<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Serializer;

use DateTime;
use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\Tests\Serializer\data\CustomHandler;
use InvalidArgumentException;
use JMS\Serializer\Context;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class FactoryTest extends TestCase
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
        /** @var array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config */
        $config = [
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                CustomHandler::class,
            ],
        ];

        $serializer = (new Factory())->getSerializer(Config::fromConfig($config));

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
                static function (JsonSerializationVisitor $visitor, DateTime $date, array $type, Context $context) {
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

        /** @phpstan-ignore-next-line  */
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

    #[Test]
    public function detectIfSerializerHasDefaultListeners(): void
    {
        $expectedSerializer = $this->getSerializer();

        /** @var array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $config */
        $config = [
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                CustomHandler::class,
            ],
        ];

        $serializer = (new Factory())->getSerializer(Config::fromConfig($config));

        $this->assertEquals($expectedSerializer, $serializer);
    }

    private function getSerializer(): Serializer
    {
        /** @var array{serialize_null: bool, cache_dir: string, serialize_type: string, debug: bool, add_default_handlers: bool, custom_handlers: array<int, CustomHandlerConfiguration>} $options */
        $options = [
            'serialize_null' => true,
            'serialize_type' => 'json',
            'cache_dir' => 'tmp',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [
                CustomHandler::class,
            ],
        ];

        $config = Config::fromConfig($options);

        $builder = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->addDefaultListeners()
            ->setSerializationContextFactory(static function () use ($config): SerializationContext {
                return SerializationContext::create()->setSerializeNull($config->shouldSerializeNull());
            });

        if ($config->shouldAddDefaultHeaders()) {
            $builder->addDefaultHandlers();
        }

        $customHandlers = $config->getCustomHandlers();
        if ($customHandlers !== []) {
            $builder->configureHandlers(function (HandlerRegistry $registry) use ($customHandlers): void {
                foreach ($customHandlers as $customHandler) {
                    if (is_string($customHandler) && class_exists($customHandler)) {
                        $customHandler = new $customHandler();
                    }

                    Assert::implementsInterface(
                        $customHandler,
                        CustomHandlerConfiguration::class,
                        sprintf(
                            'Its required to implement the "%s" interface',
                            CustomHandlerConfiguration::class
                        )
                    );
                    /** @phpstan-ignore-next-line */
                    assert($customHandler instanceof CustomHandlerConfiguration);

                    $registry->registerHandler(
                        $customHandler->getDirection(),
                        $customHandler->getTypeName(),
                        $customHandler->getFormat(),
                        $customHandler->getCallable(),
                    );
                }
            });
        }

        $cacheDir = $config->getCacheDir();
        if ($cacheDir !== '') {
            $builder->setCacheDir($cacheDir);
        }

        return $builder->setDebug($config->debug())->build();
    }
}
