<?php

namespace App\Http\Controllers;

use App\Http\Response\Time\GetTimeResponse;
use App\Model\ApplicationSetting\Repository\ApplicationSettingRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Response\Time\Exception\NegativeTimeException;

/**
 * A controller for handling user requests that relate to the current application time.
 */
class ApplicationTimeController extends Controller
{
    /**
     * Handles a request from a user to get the current application time.
     *
     * @param ApplicationSettingRepository $settingRepository
     * @return GetTimeResponse
     * @throws NegativeTimeException
     */
    public function index(ApplicationSettingRepository $settingRepository): JsonResponse
    {
        return new GetTimeResponse($settingRepository->findOneById('time')->getValue());
    }

}
