* [Back](../README.md)
* [ResponseFactory](response-factory.md)

# Configuration

The basic configuration provided by this package itself

```php
<?php 
declare(strict_types=1);

return [ 
    'serialize_null' => true,
    'serialize_type' => Config\Config::SERIALIZE_TYPE_JSON,
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [],
];
```

## How to use Custom-Handlers

If you want to add custom handlers, please use the following interface provided with this package.

Example:
```php
use Dropelikeit\LaravelJmsSerializer\Contracts\CustomHandlerConfiguration;

final class CustomHandler implements CustomHandlerConfiguration
{
    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_SERIALIZATION;
    }

    public function getTypeName(): string
    {
        return 'DateTime';
    }

    public function getFormat(): string
    {
        return 'json';
    }

    public function getCallable(): callable
    {
        return static function (JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context) {
            return $date->format($type['params'][0]);
        };
    }
}
```

If you want to use the above handler in Serializer, you have two options to add the handler class to your configuration:

Example one:
```php
<?php 
declare(strict_types=1);

return [ 
    'serialize_null' => true,
    'serialize_type' => Config\Config::SERIALIZE_TYPE_JSON,
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [
        CustomHandler::class,
    ],
];
```

Example two:
```php
<?php 
declare(strict_types=1);

return [ 
    'serialize_null' => true,
    'serialize_type' => Config\Config::SERIALIZE_TYPE_JSON,
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [
        new CustomHandler(),
    ],
];
```