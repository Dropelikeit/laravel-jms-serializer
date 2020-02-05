<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response\Item;
use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class Response extends ArrayIterator
{
    private function __construct(array $items)
    {
        Assert::allIsInstanceOf($items, Item::class);
        Assert::isList($items);

        parent::__construct($items);
    }

    public static function create(array $items): self
    {
        return new self($items);
    }
}
