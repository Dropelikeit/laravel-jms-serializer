<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Contracts;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;
use Dropelikeit\LaravelJmsSerializer\Config\Config;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface ResponseBuilder
{
    /**
     * @psalm-param positive-int $code
     */
    public function withStatusCode(int $code): void;

    public function withContext(SerializationContext $context): void;

    /**
     * @psalm-param Config::SERIALIZE_TYPE_* $serializeType
     */
    public function withSerializeType(string $serializeType): self;

    public function create(object $jmsResponse): Response;

    /**
     * @param array<int|string, mixed> $jmsResponse
     *
     * @return Response
     */
    public function createFromArray(array $jmsResponse): Response;
}
