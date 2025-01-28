![Gitworkflow](https://github.com/Dropelikeit/laravel-jms-serializer/actions/workflows/ci.yml/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/Dropelikeit/laravel-jms-serializer/badge.svg?branch=master)](https://coveralls.io/github/Dropelikeit/laravel-jms-serializer?branch=master)
[![Monthly Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/d/monthly)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Daily Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/d/daily)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Total Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/downloads)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Latest Stable Version](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/v/stable)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Total Downloads](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/downloads)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![License](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/license)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![composer.lock](https://poser.pugx.org/dropelikeit/laravel-jms-serializer/composerlock)](https://packagist.org/packages/dropelikeit/laravel-jms-serializer)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FDropelikeit%2Flaravel-jms-serializer%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/Dropelikeit/laravel-jms-serializer/master)

# JMS Serializer for Laravel

This package integrates the JMS serializer into Laravel.

JMS-Serializer: https://github.com/schmittjoh/serializer

You are also welcome to use the Issue Tracker to set bugs, improvements or upgrade requests.

> Please use the new package https://github.com/Dropelikeit/laravel-responsefactory in the future. The new package contains some new and useful functions!
This package will only be supported until the end of Laravel 11. Until then, you should use the new package.

### Installation

``` composer require dropelikeit/laravel-jms-serializer ```

### Support note
| Laravel |        PHP         | Package Version |    Status     |
|:-------:|:------------------:|:---------------:|:-------------:|
|   11    |      8.2, 8.3      |     v6.x.x      |    Support    |
|   10    |   8.1, 8.2, 8.3    |    >=v5.x.x     |    Support    |
|    9    |   8.0, 8.1, 8.2    | v4.x.x - v5.1.0 | Not supported |
|    8    | 7.3, 7.4, 8.0, 8.1 | v3.x.x - v4.0.0 | Not supported |
|    7    | 7.2, 7.3, 7.4, 8.0 | v2.x.x - v3.0.0 | Not supported |
|    6    | 7.2, 7.3, 7.4, 8.0 | v1.x.x - v3.0.0 | Not supported |

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
