<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\data\ResponseFactory;

use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot('XmlDummy')]
final class XmlDummy
{
    #[XmlAttribute]
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
