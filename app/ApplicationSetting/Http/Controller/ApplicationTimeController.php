<?php

namespace App\ApplicationSetting\Http\Controller;

use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\ApplicationSetting\Exception\InvalidNumberOfSecondsException;
use App\Http\Controllers\Controller;
use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\Lib\DateTime\Exception\InvalidDateFormatException;
use App\Lib\DateTime\Format;
use App\Lib\Enum\Http\Request\Header;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Tactician\CommandBus;
use InvalidArgumentException;

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
     * @return GetApplicationTimeResponse
     * @throws ApplicationTimeNotFoundException
     */
    public function index(ApplicationSettingRepository $settingRepository): GetApplicationTimeResponse
    {
        return new GetApplicationTimeResponse($settingRepository->getApplicationTime());
    }

    /**
     * Handles a request from a user to update the current application time.
     *
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidNumberOfSecondsException
     */
    public function update(CommandBus $commandBus, Request $request): JsonResponse
    {
        try {
            $observedTime = $this->resolveObservedTimeFromRequest($request);
            $command = new IncrementApplicationTimeCommand(static::SECONDS_TO_INCREMENT_BY, $observedTime);
            $commandBus->handle($command);
            $response = $this->callAction('index');
        } catch (InvalidDateFormatException|ApplicationTimeDoesNotMatchObservedTimeException $exception) {
            $response = new JsonResponse([], Response::HTTP_PRECONDITION_FAILED);
        }
        return $response;
    }

    /**
     * Resolves the time as observed from a user.
     *
     * @param Request $request
     * @return Carbon|null
     * @throws InvalidDateFormatException
     */
    protected function resolveObservedTimeFromRequest(Request $request)
    {
        $observedTime = null;
        $rawObservedTime = $request->header(Header::IF_MATCH);
        if (null !== $rawObservedTime) {
            try {
                $observedTime = Carbon::createFromFormat(Format::DEFAULT, $rawObservedTime);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidDateFormatException();
            };
        }
        return $observedTime instanceof Carbon ? $observedTime : null;
    }
}
