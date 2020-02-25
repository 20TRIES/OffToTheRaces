<?php

namespace Tests\Unit;

use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Container\Container as ContainerContract;

trait CreatesControllersTrait
{
    /**
     * Creates a new controller instance.
     *
     * @param string $className
     * @param mixed ...$args
     * @return Controller
     */
    protected function createController(string $className, ...$args): Controller
    {
        $providedWithContainer = count($args) > 0 && null !== $args[0];
        if (! $providedWithContainer) {
            $container = $this->getMockBuilder(ContainerContract::class)->getMock();
            $args[0] = $container;
        } else {
            $container = $args[0];
        }
        $className = new $className(...$args);
        if (! $providedWithContainer) {
            $container->expects($this->any())
                ->method('call')
                ->willReturnCallback(function ($callable, $parameters) {
                    $result = null;
                    if (is_array($callable) && $callable[1] === 'index') {
                        $result = new GetApplicationTimeResponse(Carbon::now());
                    }
                    return $result;
                });
        }
        return $className;
    }
}
