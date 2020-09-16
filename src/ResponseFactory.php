<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class ResponseFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Config
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

    /**
     * @var string
     */
    private $serializeType;

    /**
     * @var string
     */
    private $cacheDir;

    public function __construct(SerializerInterface $serializer, Config $config)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->serializeType = $config->getSerializeType();
        $this->cacheDir = $config->getCacheDir();
    }

    public function withStatusCode(int $code): void
    {
        $this->status = $code;
    }

    public function withContext(SerializationContext $context): void
    {
        $this->context = $context;
    }

    public function withSerializeType(string $serializeType): self
    {
        if (!in_array($serializeType, [Config::SERIALIZE_TYPE_JSON, Config::SERIALIZE_TYPE_XML], true)) {
            throw SerializeType::fromUnsupportedSerializeType($serializeType);
        }

        $instance = new self($this->serializer, $this->config);
        $instance->serializeType = $serializeType;

        return $instance;
    }

    public function create(object $jmsResponse): JsonResponse
    {
        $initialType = $this->getInitialType($jmsResponse);

        $content = $this->serializer->serialize(
            $jmsResponse,
            $this->serializeType,
            $this->context,
            $initialType
        );

        return new JsonResponse($content, $this->status, ['application/json'], true);
    }

    public function createFromArray(array $jmsResponse): JsonResponse
    {
        $content = $this->serializer->serialize($jmsResponse, $this->serializeType, $this->context);

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
