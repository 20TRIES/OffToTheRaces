<?php

namespace App\ApplicationSetting\Controller;

use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\Http\Controllers\Controller;
use App\ApplicationSetting\Response\GetTimeResponse;
use App\ApplicationSetting\ApplicationSettingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Tactician\CommandBus;

/**
 * A controller for handling user requests that relate to the current application time.
 */
class ApplicationTimeController extends Controller
{
    /**
     * @var string
     */
    const SECONDS_TO_INCREMENT_BY = 10;

    /**
     * Handles a request from a user to get the current application time.
     *
     * @param ApplicationSettingRepository $settingRepository
     * @return GetTimeResponse
     */
    public function index(ApplicationSettingRepository $settingRepository): JsonResponse
    {
        return new GetTimeResponse($settingRepository->findOneById('time')->getValue());
    }

    /**
     * Handles a request from a user to update the current application time.
     *
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     */
    public function update(CommandBus $commandBus, Request $request): JsonResponse
    {
        $observedTime = (int) $request->header('If-Match', '-1');
        if ($observedTime < 0) {
            $observedTime = null;
        }
        $command = new IncrementApplicationTimeCommand(static::SECONDS_TO_INCREMENT_BY, $observedTime);
        try {
            $updatedTime = $commandBus->handle($command);
            $response = new GetTimeResponse($updatedTime);
        } catch (ApplicationTimeDoesNotMatchObservedTimeException $exception) {
            $response = new JsonResponse([], Response::HTTP_PRECONDITION_FAILED);
        }
        return $response;
    }
}
