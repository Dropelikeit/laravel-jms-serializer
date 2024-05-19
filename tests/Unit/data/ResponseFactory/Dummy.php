<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Unit\data\ResponseFactory;

use Dropelikeit\LaravelJmsSerializer\Tests\Unit\data\ResponseFactory\Response\Item;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Dummy
{
    /**
     * @Serializer\Type("integer")
     */
    private int $amount = 12;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private string $text = 'Hello World!';

    /**
     * @Serializer\Type("array<Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response\Item>")
     * @var array<int, Item>|null
     * @psalm-param Item
     */
    public ?array $items = null;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return array<int, Item>|null
     */
    public function getItems(): ?array
    {
        return $this->items;
    }
}
