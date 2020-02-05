# JMS Serializer for Laravel

This package integrates the JMS serializer into Laravel.

JMS-Serializer: https://github.com/schmittjoh/serializer

You are also welcome to use the Issue Tracker to set bugs, improvements or upgrade requests.

### Installation

``` composer require dropelikeit/laravel-jms-serializer ```



### How to use

Laravel 5.5 and later uses Package Auto-Discovery, so you do not need to add the service provider manually. 
For Laravel versions below 5.5, the package must be added manually, add the following line in the "providers" array in config/app.php:

```php 
    Dropelikeit\LaravelJmsSerializer\ServiceProvider::class,
```

For example, to use the JMS serializer in a controller, add the ResponseFactory in the constructor.

```php
    <?php 
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    class ExampleController extends Controller 
    {
        /**
        * @var ResponseFactory  
        */
        private $responseFactory;

        public function __construct(ResponseFactory $responseFactory) 
        {
            $this->responseFactory = $responseFactory;
        }

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

After that you will see a config file in your config folder named "laravel-jms-serializer.php" with the following content:


```php
<?php 
return [ 
    'serialize_null' => true,
    'serialize_type' => Config\Config::SERIALIZE_TYPE_JSON,
    'debug' => false,
];
```

As you can see zero values are serialized by default.

## Using Custom-Context

To use your own JMS contexts, use the "withContext" method

To learn more about JMS context, read the JMS Serializer documentation: http://jmsyst.com/libs/serializer/master

```php
    <?php 
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use JMS\Serializer\SerializationContext;

    class ExampleController extends Controller 
    {
        /**
        * @var ResponseFactory  
        */
        private $responseFactory;

        public function __construct(ResponseFactory $responseFactory) 
        {
            $this->responseFactory = $responseFactory;
        }

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');

            $context = SerializationContext::create()->setSerializeNull(true);

            $this->responseFactory->withContext($context);
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```

## Using Status-Code

You do not always want to hand over a status code of 200 to the frontend. You can achieve this with the following code. Use the method "withStatusCode" for this

```php
    <?php 
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    class ExampleController extends Controller 
    {
        /**
        * @var ResponseFactory  
        */
        private $responseFactory;

        public function __construct(ResponseFactory $responseFactory) 
        {
            $this->responseFactory = $responseFactory;
        }

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');

            $this->responseFactory->withStatusCode(400);
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```



