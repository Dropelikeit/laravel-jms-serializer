<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Unit\Serializer\data;

use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class CustomHandler implements CustomHandlerConfiguration
{
    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_SERIALIZATION;
    }

    public function getTypeName(): string
    {
        return 'DateTime';
    }

    public function getFormat(): string
    {
        return 'json';
    }

    public function getCallable(): callable
    {
        return static function (JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context) {
            return $date->format($type['params'][0]);
        };
    }
}
