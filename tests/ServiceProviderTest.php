<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\ResponseBuilder;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Dropelikeit\LaravelJmsSerializer\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
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

    public function setUp(): void
    {
        $this->application = $this->createMock(Application::class);
        $this->configRepository = $this->createMock(Repository::class);
    }

    /**
     * @test
     */
    public function canRegister(): void
    {
        $this->markTestSkipped();

        $config = Config::fromConfig([
            'serialize_null' => true,
            'cache_dir' => 'tmp',
            'serialize_type' => 'json',
            'debug' => false,
        ]);

        $this->application
            ->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->application
            ->expects(self::once())
            ->method('storagePath')
            ->willReturn('tmp');

        $this->configRepository
            ->expects(self::exactly(4))
            ->method('get')
            ->withConsecutive([
                ['laravel-jms-serializer', []],
                ['laravel-jms-serializer.serialize_null', true],
                ['laravel-jms-serializer.serialize_type', 'json'],
                ['laravel-jms-serializer.debug', false],
            ])
            ->willReturnOnConsecutiveCalls([
                [], true, 'json', false,
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
            ->withConsecutive([
                [ResponseBuilder::class, ResponseFactory::class],
                ['ResponseFactory', static function ($app): ResponseFactory {
                    return $app->get(ResponseFactory::class);
                }]
            ]);

        $this->application
            ->expects(self::once())
            ->method('make')
            ->with('config')
            ->willReturn($this->configRepository);

        $this->configRepository
            ->expects(self::once())
            ->method('set')
            ->with('laravel-jms-serializer', [
                'serialize_null' => true,
                'serialize_type' => 'json',
                'debug' => false,
            ]);

        $provider = new ServiceProvider($this->application);

        $provider->register();
    }
}
