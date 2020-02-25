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
     * @param iterable $races
     * @param int|null $numberOfHorsesToReport [$numberOfHorsesToReport=null]
     */
    public function __construct(Carbon $applicationTime, iterable $races, int $numberOfHorsesToReport = null)
    {
        $data = [
            'time' => [
                'current' => $applicationTime->toIso8601String(),
            ],
            'races' => [],
            'pagination' => [
                'current_page' => [
                    'size' => 0,
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
            ++$data['pagination']['current_page']['size'];
        }
        parent::__construct($data);
    }
}
