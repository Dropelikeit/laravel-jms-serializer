<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Serializer;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class Factory
{
    public function getSerializer(Config $config): SerializerInterface
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
            ->setSerializationContextFactory(function () use ($config) {
                return SerializationContext::create()
                    ->setSerializeNull($config->shouldSerializeNull())
                    ;
            })
        ->setCacheDir($config->getCacheDir())->setDebug($config->debug())->build();

        return $builder->build();
    }
}
