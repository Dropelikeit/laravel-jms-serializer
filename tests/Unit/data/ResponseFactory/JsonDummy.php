<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Unit\data\ResponseFactory;

use JMS\Serializer\Annotation as Serializer;

final class JsonDummy
{
    #[Serializer\Type(values: 'string')]
    private readonly string $title;

    public function __construct()
    {
        $this->title = 'My test';
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
