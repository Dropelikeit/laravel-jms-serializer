<?php

declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Dropelikeit\LaravelJmsSerializer\Config\Config;
use Dropelikeit\LaravelJmsSerializer\Serializer\Factory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/laravel-jms-serializer.php';
        $this->mergeConfigFrom($configPath, 'laravel-jms-serializer');

        $path = $this->app->storagePath();
        $shouldSerializeNull = $this->app['config']->get('laravel-jms-serializer.serialize_null', true);
        $serializeType = $this->app['config']->get('laravel-jms-serializer.serialize_type', Config::SERIALIZE_TYPE_JSON);
        $debug = $this->app['config']->get('laravel-jms-serializer.debug', false);

        $config = Config::fromConfig([
            'serialize_null' => $shouldSerializeNull,
            'cache_dir' => $path,
            'serialize_type' => $serializeType,
            'debug' => $debug,
        ]);

        $this->app->singleton(ResponseFactory::class, function () use ($config): ResponseFactory {
            return new ResponseFactory((new Factory())->getSerializer($config), $config);
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        AnnotationRegistry::registerLoader('class_exists');

        $configPath = __DIR__ . '/../config/laravel-jms-serializer.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'laravel-jms');
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return config_path('laravel-jms-serializer.php');
    }
}
