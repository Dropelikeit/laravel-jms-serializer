<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Exception;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use InvalidArgumentException;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class SerializeType extends InvalidArgumentException
{
    public static function fromUnsupportedSerializeType(string $type): self
    {
        return new self(
            sprintf(
                'Unknown given type "%s" allowed types are "%s" and "%s"',
                $type,
                Config::SERIALIZE_TYPE_JSON,
                Config::SERIALIZE_TYPE_XML
            ),
            400
        );
    }
}
