[![Build Status](https://travis-ci.org/Dropelikeit/laravel-jms-serializer.svg?branch=master)](https://travis-ci.org/Dropelikeit/PriceCalculator)
[![Coverage Status](https://coveralls.io/repos/github/Dropelikeit/laravel-jms-serializer/badge.svg)](https://coveralls.io/github/Dropelikeit/laravel-jms-serializer)
[![Monthly Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/d/monthly)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Daily Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/d/daily)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Total Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/downloads)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Latest Stable Version](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/v/stable)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Total Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/downloads)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![License](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/license)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![composer.lock](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/composerlock)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)

# JMS Serializer for Laravel

This package integrates the JMS serializer into Laravel.

JMS-Serializer: https://github.com/schmittjoh/serializer

You are also welcome to use the Issue Tracker to set bugs, improvements or upgrade requests.

### Installation

``` composer require dropelikeit/laravel-jms-serializer ```

### Support note
- Laravel 6 and 7 are no longer supported with release v4.0.0 and higher.  
- Laravel 5.* is no longer supported with release v2.0.0 and higher.

### How to use

Laravel uses Package Auto-Discovery, so you do not need to add the service provider manually. 

For example, to use the JMS serializer in a controller, add the ResponseFactory in the constructor.

```php
    <?php 
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```

Publish the Serializer Config with the command

```bash 
    php artisan vendor:publish
```

After that you will see a config file in your config folder named "laravel-jms-serializer.php".

## Upgrade
If you are upgrading this package from a version earlier than v4.0, see [this upgrade file](UPGRADE-4.0.md).

## Documentation

* [Configuration](docs/configuration.md)
* [ResponseFactory](docs/response-factory.md)
