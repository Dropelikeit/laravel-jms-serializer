<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Dropelikeit\LaravelJmsSerializer\Config\ConfigInterface;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Dummy;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ResponseFactoryTest extends TestCase
{
    /**
     * @var MockObject|ConfigInterface
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        AnnotationRegistry::registerLoader('class_exists');

        $this->config = $this->getMockBuilder(ConfigInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function canCreateResponse(): void
    {
        $this->config
            ->expects(self::exactly(2))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('{"amount":12,"text":"Hello World!"}', $response->getContent());
    }

    /**
     * @test
     */
    public function canCreateFromArrayIterator(): void
    {
        $this->config
            ->expects(self::exactly(2))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->create(Response::create([new Response\Item()]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('[{"key":"magic_number","value":12}]', $response->getContent());
    }

    /**
     * @test
     */
    public function canCreateResponseFromArray(): void
    {
        $this->config
            ->expects(self::exactly(2))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->createFromArray(require __DIR__ . '/ResponseFactory/dummy_array.php');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(
            '{"some_objects":{"person":{"first_name":"Max","last_name":"Mustermann","birthdate":"01.01.1976","birth_place":"Berlin","nationality":"german"}}}',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function canChangeStatusCode(): void
    {
        $this->config
            ->expects(self::exactly(2))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $responseFactory->withStatusCode(404);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('{"amount":12,"text":"Hello World!"}', $response->getContent());
    }

    /**
     * @test
     */
    public function canUseGivenContext(): void
    {
        $this->config
            ->expects(self::exactly(2))
        ->method('getCacheDir')
        ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));

        $response = $responseFactory->create(new Dummy());

        self::assertEquals('{"amount":12,"text":"Hello World!","items":null}', $response->getContent());
    }

    /**
     * @test
     */
    public function canWithSerializeType(): void
    {
        $this->config
            ->expects(self::exactly(3))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::exactly(2))
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));
        $responseFactory = $responseFactory->withSerializeType(Config::SERIALIZE_TYPE_XML);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?>
<result>
  <amount>12</amount>
  <text><![CDATA[Hello World!]]></text>
</result>
',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function canNotCreateWithUnknownSerializeType(): void
    {
        $this->expectException(SerializeType::class);

        $this->config
            ->expects(self::exactly(2))
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));
        $responseFactory->withSerializeType('array');
    }
}
