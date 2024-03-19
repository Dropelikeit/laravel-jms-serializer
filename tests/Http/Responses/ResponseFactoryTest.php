<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Http\Responses;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts;
use Dropelikeit\LaravelJmsSerializer\Exception\SerializeType;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Dummy;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\Response;
use Dropelikeit\LaravelJmsSerializer\Tests\ResponseFactory\XmlDummy;
use Illuminate\Http\Response as LaravelResponse;
use InvalidArgumentException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ResponseFactoryTest extends TestCase
{
    private readonly MockObject&Contracts\Config $config;
    private readonly MockObject&SerializerInterface $serializer;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = $this
            ->getMockBuilder(Contracts\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = $this
            ->getMockBuilder(SerializerInterface::class)
            ->getMock();
    }

    #[Test]
    public function canCreateResponse(): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('{"amount":12,"text":"Hello World!"}', $response->getContent());
    }

    #[Test]
    public function canCreateFromArrayIterator(): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->create(Response::create([new Response\Item()]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('[{"key":"magic_number","value":12}]', $response->getContent());
    }

    #[Test]
    public function canCreateJsonResponseFromArray(): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->createFromArray(require __DIR__ . '/../../ResponseFactory/dummy_array.php');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(
            '{"some_objects":{"person":{"first_name":"Max","last_name":"Mustermann","birthdate":"01.01.1976","birth_place":"Berlin","nationality":"german"}}}',
            $response->getContent()
        );
    }

    #[Test]
    public function canCreateXmlResponseFromArray(): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn('xml');

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->createFromArray(require __DIR__ . '/../../ResponseFactory/dummy_array.php');

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?>
<result>
  <entry>
    <entry>
      <entry><![CDATA[Max]]></entry>
      <entry><![CDATA[Mustermann]]></entry>
      <entry><![CDATA[01.01.1976]]></entry>
      <entry><![CDATA[Berlin]]></entry>
      <entry><![CDATA[german]]></entry>
    </entry>
  </entry>
</result>
',
            $response->getContent()
        );
    }

    #[Test]
    public function canChangeStatusCode(): void
    {
        $this->config
            ->expects(self::once())
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

    #[Test]
    public function canUseGivenContext(): void
    {
        $this->config
            ->expects(self::once())
        ->method('getCacheDir')
        ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));

        $response = $responseFactory->create(new Dummy());

        self::assertEquals('{"amount":12,"text":"Hello World!","items":null}', $response->getContent());
    }

    /**
     * @psalm-param Contracts\Config::SERIALIZE_TYPE_* $changeSerializeTypeTo
     */
    #[Test]
    #[DataProvider(methodName: 'dataProviderCanSerializeWithSerializeType')]
    public function canSerializeWithSerializeType(string $changeSerializeTypeTo, string $expectedResult): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::exactly(2))
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));
        $responseFactory = $responseFactory->withSerializeType($changeSerializeTypeTo);

        $response = $responseFactory->create(new Dummy());

        self::assertEquals(
            $expectedResult,
            $response->getContent()
        );
    }

    /**
     * @return array<string, array<int, string>>
     * @psalm-return array{with_json: array<int, string>, 'with_xml': array<int, string>}
     */
    public static function dataProviderCanSerializeWithSerializeType(): array
    {
        return [
            'with_json' => [
                Contracts\Config::SERIALIZE_TYPE_JSON,
                '{"amount":12,"text":"Hello World!"}',
            ],
            'with_xml' => [
                Contracts\Config::SERIALIZE_TYPE_XML,
                '<?xml version="1.0" encoding="UTF-8"?>
<result>
  <amount>12</amount>
  <text><![CDATA[Hello World!]]></text>
</result>
',
            ],
        ];
    }

    #[Test]
    public function canNotCreateWithUnknownSerializeType(): void
    {
        $this->expectException(SerializeType::class);

        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);
        $responseFactory->withContext(SerializationContext::create()->setSerializeNull(true));
        /** @phpstan-ignore-next-line */
        $responseFactory->withSerializeType('array');
    }

    #[Test]
    public function canCreateQuietResponse(): void
    {
        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->createQuietResponse();

        $this->assertEquals(new LaravelResponse(status: 204), $response);
    }

    #[Test]
    public function throwInvalidArgumentExceptionIfContentOnCreateMethodIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Expected a different value than "".');

        $object = new Dummy();

        $this->config
            ->expects(self::never())
            ->method('getCacheDir');

        $this->config
            ->expects(self::never())
            ->method('debug');

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $this->serializer
            ->expects(self::once())
            ->method('serialize')
            ->with($object, 'json', null, null)
            ->wilLReturn('');

        (new ResponseFactory($this->serializer, $this->config))->create($object);
    }

    #[Test]
    public function throwInvalidArgumentExceptionIfContentOnCreateFromArrayMethodIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('Expected a different value than "".');

        $object = new Dummy();

        $this->config
            ->expects(self::never())
            ->method('getCacheDir');

        $this->config
            ->expects(self::never())
            ->method('debug');

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn(Contracts\Config::SERIALIZE_TYPE_JSON);

        $this->serializer
            ->expects(self::once())
            ->method('serialize')
            ->with([$object], 'json', null)
            ->wilLReturn('');

        (new ResponseFactory($this->serializer, $this->config))->createFromArray([$object]);
    }

    #[Test]
    public function canDetectIfSerializeTypeIsXmlResultResponseHasXml(): void
    {
        $this->config
            ->expects(self::once())
            ->method('getCacheDir')
            ->willReturn(__DIR__);

        $this->config
            ->expects(self::once())
            ->method('debug')
            ->willReturn(true);

        $this->config
            ->expects(self::once())
            ->method('getSerializeType')
            ->willReturn('xml');

        $responseFactory = new ResponseFactory((new Factory())->getSerializer($this->config), $this->config);

        $response = $responseFactory->create(new XmlDummy());

        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?>
<XmlDummy title="My test"/>
',
            $response->getContent(),
        );
    }
}
