<?php

namespace App\Race\Http\Response;

use App\Horse\HorseModel;
use App\Lib\DateTime\Format;
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
                'current' => $applicationTime->format(Format::DEFAULT),
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
            $raceFinishTime = $race->getFinishedAt();
            $raceLength = $race->getLength();
            $raceData = [
                'id' => $race->getShortName(),
                'name' => $race->getName(),
                'finished_at' => $raceFinishTime->gt($applicationTime) ? null : $raceFinishTime->format(Format::DEFAULT),
                'length' => $raceLength,
                'horses' => [],
            ];
            $raceStartTime = $race->calculateStartTime();
            $raceData['start_time'] = $raceStartTime->format(Format::DEFAULT);
            $secondsIntoRace = $raceStartTime->diffInSeconds($applicationTime);
            $currentPosition = 0;
            $distanceAchievedAtLastPosition = -1;
            $timeTakenAtLastPosition = -1;
            foreach ($race->getHorses()->sortByDistanceCoveredAfterNSeconds($secondsIntoRace) as $horse) {
                assert($horse instanceof HorseModel);
                if (null !== $numberOfHorsesToReport && count($raceData['horses']) >= $numberOfHorsesToReport) {
                    break;
                }
                $distanceCovered = (int) floor(min($secondsIntoRace ? $horse->calculateMetersCoverableInNSeconds($secondsIntoRace) : 0, $raceLength));
                $secondsForHorseToCompleteRace = $horse->getPerformance()->getSecondsToFinish();
                $horseHasCompletedRace = $distanceCovered === $raceLength;
                $secondsHorseHasRun = min($secondsIntoRace, $secondsForHorseToCompleteRace);;

                if ($horseHasCompletedRace) {
                    if ($secondsForHorseToCompleteRace !== $timeTakenAtLastPosition) {
                        $distanceAchievedAtLastPosition = $raceLength;
                        $timeTakenAtLastPosition = $secondsForHorseToCompleteRace;
                        ++$currentPosition;
                    }
                } elseif ($distanceCovered !== $distanceAchievedAtLastPosition) {
                    $distanceAchievedAtLastPosition = $distanceCovered;
                    $timeTakenAtLastPosition = $secondsHorseHasRun;
                    ++$currentPosition;
                }
                $raceData['horses'][] = [
                    'id' => $horse->getShortName(),
                    'name' => $horse->getName(),
                    'distance_covered' => $distanceCovered,
                    'seconds_run' => $secondsHorseHasRun,
                    'position' => $currentPosition,
                ];
            }
            $data['races'][] = $raceData;
            ++$data['pagination']['current_page']['size'];
        }
        parent::__construct($data);
    }
}
