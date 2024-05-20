<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Feature;

use Dropelikeit\LaravelJmsSerializer\Contracts\ResponseBuilder;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    private readonly Application $application;

    public function setUp(): void
    {
        parent::setUp();

        $configRepository = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects(self::once())
            ->method('set')
            ->with('laravel-jms-serializer', [
                'serialize_null' => true,
                'serialize_type' => 'json', // Contracts\Config::SERIALIZE_TYPE_XML
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $configRepository
            ->expects(self::exactly(6))
            ->method('get')
            ->willReturnOnConsecutiveCalls([], true, 'json', false, true, []);

        $this->application = new Application();

        $this->application->bind('config', fn () => $configRepository);

        $this->application->register(ServiceProvider::class);
    }

    /**
     * @test
     */
    public function canBuildResponseFactoryByIdFromConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get('ResponseFactory');

        $this->assertInstanceOf(ResponseBuilder::class, $responseFactory);
    }

    /**
     * @test
     */
    public function canBuildResponseFactoryByClassConfiguredServiceProvider(): void
    {
        $responseFactory = $this->application->get(ResponseFactory::class);

        $this->assertInstanceOf(ResponseBuilder::class, $responseFactory);
    }
}
