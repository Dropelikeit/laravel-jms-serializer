<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\data\ResponseFactory\Response;

use JMS\Serializer\Annotation as Serializer;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class Item
{
    /**
     * @Serializer\Type("string")
     * @var string
     */
    private string $key = 'magic_number';

    /**
     * @Serializer\Type("integer")
     * @var int
     */
    private int $value = 12;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
