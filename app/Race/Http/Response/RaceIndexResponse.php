<?php

namespace App\Race\Http\Response;

use App\Horse\HorseModel;
use App\Race\RaceModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * A response for showing an index of race entities
 */
class RaceIndexResponse extends JsonResponse
{
    /**
     * @param Carbon $applicationTime
     * @param int|null $numberOfHorsesToReport [$numberOfHorsesToReport=null]
     * @param array<RaceModel> $races
     */
    public function __construct(Carbon $applicationTime, array $races, int $numberOfHorsesToReport = null)
    {
        $data = [
            'time' => [
                'current' => $applicationTime->toIso8601String(),
            ],
            'races' => [],
            'pagination' => [
                'current_page' => [
                    'size' => count($races),
                ],
            ],
        ];
        foreach ($races as $race) {
            assert($race instanceof RaceModel);
            $finishTime = $race->getFinishedAt();
            $raceData = [
                'id' => $race->getShortName(),
                'name' => $race->getName(),
                'finished_at' => $finishTime->gt($applicationTime) ? null : $finishTime->toDateTimeString(),
                'horses' => [],
            ];
            $timesToFinish = [];
            foreach ($race->horses as $horse) {
                assert($horse instanceof HorseModel);
                $timesToFinish[] = $horse->getRaceHorsePerformance()->getTimeToFinish();
            }
            $raceStartTime = $finishTime->subSeconds(max($timesToFinish));
            $raceData['start_time'] = $raceStartTime->toIso8601String();
            $secondsElapsedSinceBeginningOfRace = $raceStartTime->diffInSeconds($applicationTime);
            foreach ($race->horses as $horse) {
                assert($horse instanceof HorseModel);
                $raceData['horses'][] = [
                    'id' => $horse->getShortName(),
                    'name' => $horse->getName(),
                    'distance_covered' => $secondsElapsedSinceBeginningOfRace < 1 ? 0 : $horse->calculateMetersCoverableInNSeconds($secondsElapsedSinceBeginningOfRace),
                ];
                $timesToFinish[] = $horse->getRaceHorsePerformance()->getTimeToFinish();
            }
            usort($raceData['horses'], function ($a, $b) {
                $aVal = $a['distance_covered'] ?? 0;
                $bVal = $b['distance_covered'] ?? 0;
                return $aVal == $bVal ? 0 : ($aVal < $bVal) ? -1 : 1;
            });
            foreach ($raceData['horses'] as $key => $horse) {
                $raceData['horses'][$key]['position'] = $key + 1;
            }
            if (null !== $numberOfHorsesToReport) {
                $raceData['horses'] = array_slice($raceData['horses'], 0, $numberOfHorsesToReport);
            }
            $data['races'][] = $raceData;
        }
        parent::__construct($data);
    }
}
