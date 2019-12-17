<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class Dummy
{
    /**
     * @Serializer\Type("integer")
     * @var int
     */
    private $amount = 12;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $text = 'Hello World!';
}
