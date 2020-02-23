<?php

namespace App\Race\Http\Controller;

use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\Http\Controllers\Controller;
use App\Race\Http\Response\RaceIndexResponse;
use App\Race\RaceRepository;

class FinishedRaceController extends Controller
{
    /**
     * @var int
     */
    const DEFAULT_NUMBER_OF_RESULTS = 5;

    /**
     * Handles a request from a user to get the latest races.
     *
     * @param ApplicationSettingRepository $settingRepository
     * @param RaceRepository $raceRepository
     * @return RaceIndexResponse
     * @throws ApplicationTimeNotFoundException
     */
    public function index(ApplicationSettingRepository $settingRepository, RaceRepository $raceRepository)
    {
        $applicationTime = $settingRepository->getApplicationTime();
        $races = $raceRepository->getLastNRacesThatEndOnOrBefore(static::DEFAULT_NUMBER_OF_RESULTS,$applicationTime)
            ->load(['horses'])
            ->all();
        return new RaceIndexResponse($applicationTime, $races);
    }
}
