<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
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

    public function __construct(SerializerInterface $serializer, Config $config)
    {
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function withStatusCode(int $code): void
    {
        $this->status = $code;
    }

    public function create(object $jmsResponse): JsonResponse
    {
        $content = $this->serializer->serialize($jmsResponse, $this->config->getSerializeType());

        return new JsonResponse($content, $this->status, ['application/json'], true);
    }
}
