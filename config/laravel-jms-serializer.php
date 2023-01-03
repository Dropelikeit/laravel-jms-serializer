<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer;

return [
    'serialize_null' => true,
    'serialize_type' => Contracts\Config::SERIALIZE_TYPE_JSON, // Contracts\Config::SERIALIZE_TYPE_XML
    'debug' => false,
    'add_default_handlers' => true,
    'custom_handlers' => [],
];
