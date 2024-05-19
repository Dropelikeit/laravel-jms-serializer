<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Unit\data\ResponseFactory;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Tests\Unit\data\ResponseFactory\Response\Item;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 * @extends ArrayIterator<int, Item>
 */
final class Response extends ArrayIterator
{
    /**
     * @param array<int, Item> $items
     * @psalm-param Item $items
     */
    private function __construct(array $items)
    {
        Assert::allIsInstanceOf($items, Item::class);
        Assert::isList($items);

        parent::__construct($items);
    }

    /**
     * @param array<int, Item> $items
     * @psalm-param Item $items
     */
    public static function create(array $items): self
    {
        return new self($items);
    }
}
