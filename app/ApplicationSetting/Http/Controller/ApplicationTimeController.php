<?php

namespace App\ApplicationSetting\Http\Controller;

use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\Exception\NegativeTimeException;
use App\Http\Controllers\Controller;
use App\ApplicationSetting\Http\Response\GetTimeResponse;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\Lib\Enum\Http\Request\Header;
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
     * @throws NegativeTimeException
     */
    public function index(ApplicationSettingRepository $settingRepository): GetTimeResponse
    {
        return new GetTimeResponse($settingRepository->findOneById(ApplicationSettingName::TIME)->getValue());
    }

    /**
     * Handles a request from a usser to update the current application time.
     *
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     * @throws NegativeTimeException
     */
    public function update(CommandBus $commandBus, Request $request): JsonResponse
    {
        $observedTime = $request->header(Header::IF_MATCH);
        if (null === $observedTime || $observedTime > 0) {
            $command = new IncrementApplicationTimeCommand(static::SECONDS_TO_INCREMENT_BY, $observedTime);
            try {
                $updatedTime = $commandBus->handle($command);
                $response = new GetTimeResponse($updatedTime);
            } catch (ApplicationTimeDoesNotMatchObservedTimeException $exception) {}
        }
        return $response ?? new JsonResponse([], Response::HTTP_PRECONDITION_FAILED);
    }
}