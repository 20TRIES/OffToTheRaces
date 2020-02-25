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
     * @var int|null
     */
    const DEFAULT_NUMBER_OF_HORSES_TO_REPORT = 3;

    /**
     * Handles a request from a user to get the latest races of a given length.
     *
     * @param int $raceLength
     * @param ApplicationSettingRepository $settingRepository
     * @param RaceRepository $raceRepository
     * @return RaceIndexResponse
     * @throws ApplicationTimeNotFoundException
     */
    public function index(int $raceLength, ApplicationSettingRepository $settingRepository, RaceRepository $raceRepository)
    {
        $applicationTime = $settingRepository->getApplicationTime();
        $races = $raceRepository->getLastNRacesOfGivenLengthThatEndOnOrBefore(
            $raceLength,
            $applicationTime,
            static::DEFAULT_NUMBER_OF_RESULTS
        )->load(['horses']);
        return new RaceIndexResponse($applicationTime, $races, static::DEFAULT_NUMBER_OF_HORSES_TO_REPORT);
    }
}
