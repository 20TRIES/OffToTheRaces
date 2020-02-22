<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @var ContainerContract
     */
    protected $container;

    /**
     * @param ContainerContract $container
     */
    public function __construct(ContainerContract $container)
    {
        $this->container = $container;
    }

    /**
     * Execute an action on the controller.
     *
     * @param string $method
     * @param array $parameters [$parameters=[]]
     * @return mixed
     */
    public function callAction($method, $parameters = [])
    {
        return $this->container->call([$this, $method], $parameters);
    }

}
