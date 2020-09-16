<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory;

use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response\Item;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Dummy
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

    /**
     * @Serializer\Type("array<Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response\Item>")
     * @var array<int, Item>|null
     * @psalm-param list<Item>
     */
    public $items;
}
