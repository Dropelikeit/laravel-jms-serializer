<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Exception;

use InvalidArgumentException;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class MissingRequiredItems extends InvalidArgumentException
{
    public static function fromConfig(string $fields): self
    {
        return new self(
            sprintf(
                'Missing required fields, please check your serializer-config. Missing fields "%s"',
                $fields
            ),
            400
        );
    }
}
