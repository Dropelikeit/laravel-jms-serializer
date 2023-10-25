<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Serializer;

use function assert;
use function class_exists;
use Dropelikeit\LaravelJmsSerializer\Contracts\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use function is_string;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use function sprintf;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Factory
{
    public function getSerializer(Config $config): SerializerInterface
    {
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
