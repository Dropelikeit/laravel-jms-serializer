<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Feature;

use Dropelikeit\LaravelJmsSerializer\Contracts\ResponseBuilder;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\ServiceProvider;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    private readonly Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();

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