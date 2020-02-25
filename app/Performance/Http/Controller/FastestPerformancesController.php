<?php

namespace App\Performance\Http\Controller;

use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\Http\Controllers\Controller;
use App\Performance\Http\Response\PerformancesIndexResponse;
use App\Performance\RaceHorsePerformanceRepository;
use Illuminate\Http\JsonResponse;

class FastestPerformancesController extends Controller
{
    /**
     * @var int
     */
    const DEFAULT_NUMBER_OF_HORSES_TO_REPORT = 1;

    /**
     * Handles a request from a user to get the fastest completed races.
     *
     * @param int $raceLength
     * @param ApplicationSettingRepository $settingRepository
     * @param RaceHorsePerformanceRepository $performanceRepository
     * @return JsonResponse
     * @throws ApplicationTimeNotFoundException
     */
    public function index(int $raceLength, ApplicationSettingRepository $settingRepository, RaceHorsePerformanceRepository $performanceRepository)
    {
        $applicationTime = $settingRepository->getApplicationTime();
        $performances = $performanceRepository->getFastestNPerformancesInRacesOfGivenLengthAndCompletedOnOrBeforeGivenTime(
            $raceLength,
            $applicationTime,
            static::DEFAULT_NUMBER_OF_HORSES_TO_REPORT
        );
        $performances->load(['race', 'horse']);
        return new PerformancesIndexResponse($applicationTime, $performances);
    }
}
