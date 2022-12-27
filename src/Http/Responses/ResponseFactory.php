<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Http\Responses;

use ArrayIterator;
use Dropelikeit\LaravelJmsSerializer\Contracts;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use Illuminate\Http\Response as LaravelResponse;
use Webmozart\Assert\Assert;
use function in_array;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ResponseFactory implements Contracts\ResponseBuilder
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var Contracts\Config
     */
    private Contracts\Config $config;

    /**
     * @var positive-int
     */
    private int $status;

    /**
     * @var SerializationContext|null
     */
    private ?SerializationContext $context;

    /**
     * @var string 'json'|'xml'
     */
    private string $serializeType;

    public function __construct(SerializerInterface $serializer, Contracts\Config $config)
    {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->serializeType = $config->getSerializeType();
        $this->status = Response::HTTP_OK;
        $this->context = null;
    }

    /**
     * @psalm-param positive-int $code
     */
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
        if (!in_array($serializeType, [
            Contracts\Config::SERIALIZE_TYPE_JSON,
            Contracts\Config::SERIALIZE_TYPE_XML
        ], true)) {
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
        Assert::stringNotEmpty($content);

        return $this->getResponse($content);
    }

    /**
     * @param array<int|string, mixed> $jmsResponse
     *
     * @return Response
     */
    public function createFromArray(array $jmsResponse): Response
    {
        $content = $this->serializer->serialize($jmsResponse, $this->serializeType, $this->context);
        Assert::stringNotEmpty($content);

        return $this->getResponse($content);
    }

    /**
     * @psalm-param non-empty-string $content
     */
    private function getResponse(string $content): Response
    {
        if ($this->serializeType === Contracts\Config::SERIALIZE_TYPE_XML) {
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
