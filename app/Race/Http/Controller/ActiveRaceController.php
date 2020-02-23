<?php

namespace App\Race\Http\Controller;

use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\Http\Controllers\Controller;
use App\Race\Http\Response\RaceIndexResponse;
use App\Race\RaceRepository;

class ActiveRaceController extends Controller
{
    /**
     * Handles a request from a user to get all active races.
     *
     * @param ApplicationSettingRepository $settingRepository
     * @param RaceRepository $raceRepository
     * @return RaceIndexResponse
     * @throws ApplicationTimeNotFoundException
     */
    public function index(ApplicationSettingRepository $settingRepository, RaceRepository $raceRepository)
    {
        $applicationTime = $settingRepository->getApplicationTime();
        $races = $raceRepository->getRacesThatEndAfter($applicationTime)->load(['horses'])->all();
        return new RaceIndexResponse($applicationTime, $races);
    }
}
