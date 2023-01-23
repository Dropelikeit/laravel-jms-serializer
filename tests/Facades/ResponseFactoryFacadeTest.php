<?php
declare(strict_types=1);

namespace Dropelikeit\LaravelJmsSerializer\Tests\Facades;

use Dropelikeit\LaravelJmsSerializer\Facades\ResponseFactoryFacade;
use PHPUnit\Framework\TestCase;

final class ResponseFactoryFacadeTest extends TestCase
{
    /**
     * @test
     */
    public function canInitializeFacade(): void
    {
        $object = ResponseFactoryFacade::getFacadeRoot();

        $this->assertNull($object);
    }
}
