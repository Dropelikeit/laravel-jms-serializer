<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\ResponseBuilder;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    /**
     * @psalm-var MockObject&Application
     */
    private MockObject $application;

    /**
     * @psalm-var MockObject&Repository
     */
    private MockObject $configRepository;

    private ?\Illuminate\Contracts\Container\Container $oldContainer;

    public function setUp(): void
    {
        $this->application = $this->createMock(Application::class);
        $this->configRepository = $this->createMock(Repository::class);

        $this->oldContainer = Container::getInstance();
        Container::setInstance($this->application);
    }

    /**
     * @test
     */
    public function canRegister(): void
    {
        $this->configRepository
            ->expects(self::once())
            ->method('set')
            ->with('laravel-jms-serializer', [
                'serialize_null' => true,
                'serialize_type' => 'json', // Contracts\Config::SERIALIZE_TYPE_XML
                'debug' => false,
                'add_default_handlers' => true,
                'custom_handlers' => [],
            ]);

        $this->configRepository
            ->expects(self::exactly(6))
            ->method('get')
            ->withConsecutive(
                ['laravel-jms-serializer', []],
                ['laravel-jms-serializer.serialize_null', true],
                ['laravel-jms-serializer.serialize_type', 'json'],
                ['laravel-jms-serializer.debug', false],
                ['laravel-jms-serializer.add_default_handlers', true],
                ['laravel-jms-serializer.custom_handlers', []],
            )
            ->willReturnOnConsecutiveCalls([], true, 'json', false, true, []);

        $this->application
            ->expects(self::once())
            ->method('make')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->application
            ->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->application
            ->expects(self::once())
            ->method('storagePath')
            ->with('framework/cache/data')
            ->willReturn('my-storage');

        Storage::shouldReceive('exists')->once()->with('my-storage')->andReturn(false);
        Storage::shouldReceive('makeDirectory')->with('my-storage')->andReturn(true);

        $config = Config::fromConfig([
            'serialize_null' => true,
            'cache_dir' => 'tmp',
            'serialize_type' => 'json',
            'debug' => false,
            'add_default_handlers' => true,
            'custom_handlers' => [],
        ]);

        $this->application
            ->expects(self::once())
            ->method('singleton')
            ->with(ResponseFactory::class, static function () use ($config): ResponseFactory {
                return new ResponseFactory((new Factory())->getSerializer($config), $config);
            });

        $this->application
            ->expects(self::exactly(2))
            ->method('bind')
            ->withConsecutive(
                [ResponseBuilder::class, ResponseFactory::class],
                ['ResponseFactory', static function ($app): ResponseFactory {
                    return $app->get(ResponseFactory::class);
                }]
            );

        $provider = new ServiceProvider($this->application);

        $provider->register();
    }

    /**
     * @test
     */
    public function canLoadConfigAtBootingApp(): void
    {
        $this->application
            ->expects(self::once())
            ->method('configPath')
            ->with('laravel-jms-serializer.php')
            ->willReturn('my/dir');

        $provider = new ServiceProvider($this->application);

        $provider->boot();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Container::setInstance($this->oldContainer);
    }
}
