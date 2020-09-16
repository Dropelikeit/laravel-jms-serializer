<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Config;

/**
 * @author Marcel Strahl <marcel.strahl@check24.de>
 */
interface ConfigInterface
{
    public function getCacheDir(): string;

    public function shouldSerializeNull(): bool;

    public function getSerializeType(): string;

    public function debug(): bool;
}
