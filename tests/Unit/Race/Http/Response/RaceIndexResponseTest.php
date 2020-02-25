<?php

namespace Tests\Unit\Race\Http\Response;

use App\Horse\HorseModel;
use App\Horse\HorseModelCollection;
use App\Performance\RaceHorsePerformanceModel;
use App\Race\RaceModel;
use Carbon\Carbon;
use Tests\Unit\UnitTestCase;
use App\Race\Http\Response\RaceIndexResponse;
use App\Race\Exception\InvalidRaceLengthException;

class RaceIndexResponseTest extends UnitTestCase
{
    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__itPositionsFastestHorsesFirst()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(1);

        $secondsTakenForSlowestHorseToCompleteRace = $slowestHorse->calculateSecondsToRunGivenDistance($race->getLength());
        $race->setFinishedAt($now->copy()->addSeconds($secondsTakenForSlowestHorseToCompleteRace));
        $performance = new RaceHorsePerformanceModel();
        $performance->setTimeToFinish($secondsTakenForSlowestHorseToCompleteRace);
        $slowestHorse->setRelation('pivot', $performance);

        $fastestHorse = $this->createFastestPossibleHorseWithGivenBaseSpeed(1);

        $performance = new RaceHorsePerformanceModel();
        $secondsTakenForFastestHorseToCompleteRace = $slowestHorse->calculateSecondsToRunGivenDistance($race->getLength());
        $performance->setTimeToFinish($secondsTakenForFastestHorseToCompleteRace);
        $fastestHorse->setRelation('pivot', $performance);

        $race->setRelation('horses', new HorseModelCollection([$slowestHorse, $fastestHorse]));

        $response = new RaceIndexResponse($now->copy()->addSeconds($secondsTakenForSlowestHorseToCompleteRace - 1), [$race], 2);
        $responseData = $response->getData(true);

        $this->assertEquals($fastestHorse->getShortName(), $responseData['races'][0]['horses'][0]['id']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__itStopsDistanceCoveredByHorsesFromExceedingTheLengthOfARace()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(1);

        $secondsTakenForSlowestHorseToCompleteRace = $slowestHorse->calculateSecondsToRunGivenDistance($race->getLength());
        $race->setFinishedAt($now->copy()->addSeconds($secondsTakenForSlowestHorseToCompleteRace));
        $performance = new RaceHorsePerformanceModel();
        $performance->setTimeToFinish($secondsTakenForSlowestHorseToCompleteRace);
        $slowestHorse->setRelation('pivot', $performance);

        $fastestHorse = $this->createFastestPossibleHorseWithGivenBaseSpeed(1);

        $performance = new RaceHorsePerformanceModel();
        $secondsTakenForFastestHorseToCompleteRace = $slowestHorse->calculateSecondsToRunGivenDistance($race->getLength());
        $performance->setTimeToFinish($secondsTakenForFastestHorseToCompleteRace);
        $fastestHorse->setRelation('pivot', $performance);

        $race->setRelation('horses', new HorseModelCollection([$slowestHorse, $fastestHorse]));

        $response = new RaceIndexResponse($now->copy()->addSeconds($secondsTakenForSlowestHorseToCompleteRace - 1), [$race], 2);
        $responseData = $response->getData(true);

        $this->assertEquals($race->getLength(), $responseData['races'][0]['horses'][0]['distance_covered']);
    }

    protected function createFastestPossibleHorseWithGivenBaseSpeed(float $baseSpeed)
    {
        $fastestHorse = new HorseModel();
        $fastestHorse->setShortName('Fastest Horse');
        $fastestHorse->setName('Fastest Horse');
        $fastestHorse->setBaseSpeed($baseSpeed);
        $fastestHorse->setSpeedStat(10);
        $fastestHorse->setStrengthStat(10);
        $fastestHorse->setEnduranceStat(10);
        return $fastestHorse;
    }

    protected function createSlowestPossibleHorseWithGivenBaseSpeed(float $baseSpeed)
    {
        $slowestHorse = new HorseModel();
        $slowestHorse->setShortName('Slowest Horse');
        $slowestHorse->setName('Slowest Horse');
        $slowestHorse->setBaseSpeed($baseSpeed);
        $slowestHorse->setSpeedStat(0);
        $slowestHorse->setStrengthStat(0);
        $slowestHorse->setEnduranceStat(0);
        return $slowestHorse;
    }
}
