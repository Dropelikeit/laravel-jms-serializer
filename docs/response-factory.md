* [Back](../README.md)
* [Configuration](configuration.md)

## Using Custom-Context

To use your own JMS contexts, use the "withContext" method

To learn more about JMS context, read the JMS Serializer documentation: http://jmsyst.com/libs/serializer/master

```php
    <?php 
    declare(strict_types=1);
    
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use JMS\Serializer\SerializationContext;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

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
    declare(strict_types=1);
    
    namespace App\Http\Controller;

    use Dropelikeit\LaravelJmsSerializer\Http\Responses\ResponseFactory;
    use Symfony\Component\HttpFoundation\JsonResponse;

    final class ExampleController extends Controller 
    {
        public function __construct(private ResponseFactory $responseFactory) {}

        public function myAction(): JsonResponse
        {
            $myDataObjectWithSerializerAnnotations = new Object('some data');

            $this->responseFactory->withStatusCode(400);
            return $this->responseFactory->create($myDataObjectWithSerializerAnnotations);
        }
    }
```