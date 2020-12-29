<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Config\ConfigInterface;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use Illuminate\Http\Response as LaravelResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @var string
     */
    private $serializeType;

    public function __construct(SerializerInterface $serializer, ConfigInterface $config)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->serializeType = $config->getSerializeType();
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

    public function create(object $jmsResponse): Response
    {
        $initialType = $this->getInitialType($jmsResponse);

        $content = $this->serializer->serialize(
            $jmsResponse,
            $this->serializeType,
            $this->context,
            $initialType
        );

        if ($this->serializeType === Config::SERIALIZE_TYPE_XML) {
            return new LaravelResponse($content, $this->status, ['Content-Type' => 'application/xml']);
        }

        return new JsonResponse($content, $this->status, ['Content-Type' => 'application/json'], true);
    }

    /**
     * @param array<int|string, mixed> $jmsResponse
     *
     * @return Response
     */
    public function createFromArray(array $jmsResponse): Response
    {
        $content = $this->serializer->serialize($jmsResponse, $this->serializeType, $this->context);

        if ($this->serializeType === Config::SERIALIZE_TYPE_XML) {
            return new LaravelResponse($content, $this->status, ['Content-Type' => 'application/xml']);
        }

        return new JsonResponse($content, $this->status, ['Content-Type' => 'application/json'], true);
    }

    private function getInitialType(object $jmsResponse): ?string
    {
        if ($jmsResponse instanceof ArrayIterator) {
            return 'array';
        }

        return null;
    }
}
