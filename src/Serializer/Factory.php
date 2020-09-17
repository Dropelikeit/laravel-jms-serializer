<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Serializer;

use Dropelikeit\LaravelJmsSerializer\Config\ConfigInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Factory
{
    public function getSerializer(ConfigInterface $config): SerializerInterface
    {
        $builder = new SerializerBuilder();

        $builder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->addDefaultHandlers()
            ->addDefaultListeners()
            ->setSerializationContextFactory(static function () use ($config) {
                return SerializationContext::create()
                    ->setSerializeNull($config->shouldSerializeNull())
                    ;
            })
        ->setCacheDir($config->getCacheDir())->setDebug($config->debug())->build();

        return $builder->build();
    }
}
