<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class Item
{
    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $key = 'magic_number';

    /**
     * @Serializer\Type("integer")
     * @var int
     */
    private $value = 12;
}
