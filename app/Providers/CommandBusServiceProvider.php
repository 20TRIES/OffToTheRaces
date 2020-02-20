<?php

namespace App\Providers;

use App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;

class CommandBusServiceProvider extends ServiceProvider
{
    /**
     * @var array<array<string, callable>>
     */
    const MAPPINGS = [
        IncrementApplicationTimeCommand::class => IncrementApplicationTimeHandler::class,
    ];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new ContainerLocator($this->app, static::MAPPINGS),
            new HandleInflector()
        );
        $lockingMiddleware = new LockingMiddleware();
        $commandBus =  new CommandBus([$lockingMiddleware, $handlerMiddleware]);
        $this->app->singleton(CommandBus::class, function () use ($commandBus) {
            return $commandBus;
        });
    }
}
