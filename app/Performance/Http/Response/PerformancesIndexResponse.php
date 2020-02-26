<?php

namespace App\Performance\Http\Response;

use App\Lib\DateTime\Format;
use App\Performance\RaceHorsePerformanceModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class PerformancesIndexResponse extends JsonResponse
{
    /**
     * @param Carbon $applicationTime
     * @param iterable<RaceHorsePerformanceModel> $performances
     */
    public function __construct(Carbon $applicationTime, iterable $performances)
    {
        $data = [
            'time' => [
                'current' => $applicationTime->format(Format::DEFAULT),
            ],
            'performances' => [],
            'pagination' => [
                'current_page' => [
                    'size' => 0,
                ],
            ],
        ];
        foreach ($performances as $performance) {
            assert($performance instanceof RaceHorsePerformanceModel);
            $race = $performance->getRace();
            $raceLength = $race->getLength();
            $horse = $performance->getHorse();
            $data['performances'][] = [
                'horse' => [
                    'name' => $horse->getName(),
                    'average_speed' => (float) number_format($horse->calculateAverageSpeedOverNMeters($raceLength), 2),
                    'stats' => [
                        'speed' => $horse->getSpeedStat(),
                        'strength' => $horse->getStrengthStat(),
                        'endurance' => $horse->getEnduranceStat(),
                    ],
                ],
                'race' => [
                    'name' => $race->getName(),
                    'length' => $raceLength,
                ],
            ];
            ++$data['pagination']['current_page']['size'];
        }
        parent::__construct($data);
    }
}
