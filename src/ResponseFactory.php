<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Config\ConfigInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ResponseFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var int
     */
    private $status = 200;

    /**
     * @var SerializationContext|null
     */
    private $context;

    public function __construct(SerializerInterface $serializer, ConfigInterface $config)
    {
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function withStatusCode(int $code): void
    {
        $this->status = $code;
    }

    public function withContext(SerializationContext $context): void
    {
        $this->context = $context;
    }

    public function create(object $jmsResponse): JsonResponse
    {
        $initialType = $this->getInitialType($jmsResponse);

        $content = $this->serializer->serialize(
            $jmsResponse,
            $this->config->getSerializeType(),
            $this->context,
            $initialType
        );

        return new JsonResponse($content, $this->status, ['application/json'], true);
    }

    /**
     * @param array<int|string, mixed> $jmsResponse
     *
     * @return JsonResponse
     */
    public function createFromArray(array $jmsResponse): JsonResponse
    {
        $content = $this->serializer->serialize($jmsResponse, $this->config->getSerializeType(), $this->context);

        return new JsonResponse($content, $this->status, ['application/json'], true);
    }

    private function getInitialType(object $jmsResponse): ?string
    {
        if ($jmsResponse instanceof ArrayIterator) {
            return 'array';
        }

        return null;
    }
}
