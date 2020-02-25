<?php

namespace App\Race\Http\Controller;

use App\Http\Controllers\Controller;
use App\Http\Response\Error\Code;
use App\Http\Response\Error\CodeToMessageMapper;
use App\Http\Response\Error\ErrorBuilder;
use App\Race\Command\CreateRaceCommand;
use App\Race\Exception\MaxActiveRacesAlreadyReachedException;
use App\Race\Http\Request\StoreRaceRequest;
use App\Race\Http\Response\ShowRaceResponse;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use League\Tactician\CommandBus;

/**
 * A controller for handle requests from a user to interact with races.
 */
class RaceController extends Controller
{
    /**
     * @var ErrorBuilder
     */
    protected $errorBuilder;

    /**
     * @param ContainerContract $container
     * @param ErrorBuilder $errorBuilder
     */
    public function __construct(ContainerContract $container, ErrorBuilder $errorBuilder)
    {
        parent::__construct($container);
        $this->errorBuilder = $errorBuilder;
    }

    /**
     * Handles a request from a user to create a new race.
     *
     * @param StoreRaceRequest $request
     * @param CommandBus $commandBus
     * @return JsonResponse
     */
    public function store(StoreRaceRequest $request, CommandBus $commandBus)
    {
        $input = $request->only(['name']);
        try {
            $race = $commandBus->handle(new CreateRaceCommand($input['name'] ?? null));
            $response = new ShowRaceResponse($race);
        } catch (MaxActiveRacesAlreadyReachedException $exception) {
            $response = new JsonResponse(['errors' => [
                $this->errorBuilder->build(Code::MAX_ACTIVE_RACES_REACHED),
            ]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $response;
    }
}
