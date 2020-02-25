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
            $raceStartTime = $race->calculateStartTime();
            $raceData['start_time'] = $raceStartTime->toIso8601String();
            $secondsIntoRace = $raceStartTime->diffInSeconds($applicationTime);
            foreach ($race->getHorses()->sortByDistanceCoveredAfterNSeconds($secondsIntoRace)->values() as $key => $horse) {
                assert($horse instanceof HorseModel);
                if (null !== $numberOfHorsesToReport && count($raceData['horses']) >= $numberOfHorsesToReport) {
                    break;
                }
                $raceData['horses'][] = [
                    'id' => $horse->getShortName(),
                    'name' => $horse->getName(),
                    'distance_covered' => $secondsIntoRace ? $horse->calculateMetersCoverableInNSeconds($secondsIntoRace) : 0,
                    'position' => $key + 1,
                ];
            }
            $data['races'][] = $raceData;
        }
        parent::__construct($data);
    }
}
