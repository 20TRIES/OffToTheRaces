<?php

namespace Tests\Unit;

use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\Container as ContainerContract;

trait CreatesControllersTrait
{
    /**
     * Creates a new controller instance.
     *
     * @param string $className
     * @param ContainerContract|null $customContainer
     * @return Controller
     */
    protected function createController(string $className, $customContainer = null): Controller
    {
        $providedWithContainer = $customContainer instanceof ContainerContract;
        if (! $providedWithContainer) {
            $container = $this->getMockBuilder(ContainerContract::class)->getMock();
        } else {
            $container = $customContainer;
        }
        $className = new $className($container);
        if (! $providedWithContainer) {
            $container->expects($this->any())
                ->method('call')
                ->willReturnCallback(function ($callable, $parameters) {
                    $result = null;
                    if (is_array($callable) && $callable[1] === 'index') {
                        $result = new GetApplicationTimeResponse(1);
                    }
                    return $result;
                });
        }
        return $className;
    }
}
