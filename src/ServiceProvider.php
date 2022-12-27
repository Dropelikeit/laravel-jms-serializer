<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Contracts\ResponseBuilder;
use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use function sprintf;

use Webmozart\Assert\Assert;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
final class ServiceProvider extends BaseServiceProvider
{
    /**
     * @description Register any application services.
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/laravel-jms-serializer.php';
        $this->mergeConfigFrom($configPath, 'laravel-jms-serializer');

        /** @var Repository $configRepository */
        $configRepository = $this->app->get('config');

        $path = $this->app->storagePath();
        $shouldSerializeNull = (bool) $configRepository
            ->get('laravel-jms-serializer.serialize_null', true);
        $serializeType = $configRepository
            ->get('laravel-jms-serializer.serialize_type', Contracts\Config::SERIALIZE_TYPE_JSON);
        Assert::stringNotEmpty($serializeType);
        $debug = (bool) $configRepository->get('laravel-jms-serializer.debug', false);

        $config = Config::fromConfig([
            'serialize_null' => $shouldSerializeNull,
            'cache_dir' => $path,
            'serialize_type' => $serializeType,
            'debug' => $debug,
        ]);

        $this->app->singleton(ResponseFactory::class, static function () use ($config): ResponseFactory {
            return new ResponseFactory((new Factory())->getSerializer($config), $config);
        });

        $this->app->bind(ResponseBuilder::class, ResponseFactory::class);

        $app = $this->app;
        $this->app->bind('ResponseFactory', static function ($app): ResponseFactory {
            return $app->get(ResponseFactory::class);
        });
    }

    /**
     * @description Bootstrap the application events.
     */
    public function boot(): void
    {
        $configPath = sprintf('%s/../config/laravel-jms-serializer.php', __DIR__);
        Assert::stringNotEmpty($configPath);

        $this->publishes([$configPath => $this->getConfigPath()], 'laravel-jms');
    }

    /**
     * @description Get the config path
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return config_path('laravel-jms-serializer.php');
    }
}
