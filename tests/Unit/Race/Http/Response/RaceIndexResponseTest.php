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
        $fastestHorse = $this->createFastestPossibleHorseWithGivenBaseSpeed(1);
        $this->enterHorsesIntoRace($now, $race, $slowestHorse, $fastestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish() - 1), [$race], 2);
        $responseData = $response->getData(true);

        $this->assertEquals($fastestHorse->getShortName(), $responseData['races'][0]['horses'][0]['id']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__whereHorsesHaveNotCompletedTheRace_itGivesMoreThenOneHorseWithTheSameDistanceCoveredTheSamePosition()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(1);
        $fastestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(1);
        $this->enterHorsesIntoRace($now, $race, $slowestHorse, $fastestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish() - 1), [$race], 2);
        $responseData = $response->getData(true);

        $this->assertEquals($responseData['races'][0]['horses'][0]['position'], $responseData['races'][0]['horses'][1]['position']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__whereHorsesHaveNotCompletedTheRace_itGivesMoreThenOneHorseWithTheSameDistanceCoveredTheSamePosition_andIncrementsNextHorsePositionByOne()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $horse1 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $horse2 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(3);
        $this->enterHorsesIntoRace($now->copy(), $race, $horse1, $horse2, $slowestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish() - 1), [$race], 3);
        $responseData = $response->getData(true);

        $this->assertEquals(2, $responseData['races'][0]['horses'][2]['position']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__whereHorsesHaveCompletedTheRace_itPlacesHorsesThatHaveCompletedARaceByTimeTakenToComplete()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $horse1 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(1);
        $this->enterHorsesIntoRace($now->copy(), $race, $horse1, $slowestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish()), [$race], 3);
        $responseData = $response->getData(true);

        $this->assertEquals(2, $responseData['races'][0]['horses'][1]['position']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__whereHorsesHaveCompletedTheRace_itPlacesHorsesThatHaveCompletedARaceByTimeTakenToComplete_placingThemTheSameIfTheTimesMatch()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $horse1 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $this->enterHorsesIntoRace($now->copy(), $race, $horse1, $slowestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish()), [$race], 3);
        $responseData = $response->getData(true);

        $this->assertEquals($responseData['races'][0]['horses'][0]['position'], $responseData['races'][0]['horses'][1]['position']);
    }

    /**
     * @test
     * @covers RaceIndexResponse::__construct
     *
     * @throws InvalidRaceLengthException
     */
    public function construct__whereHorsesHaveCompletedTheRace_itPlacesHorsesThatHaveCompletedARaceByTimeTakenToComplete_placingThemTheSameIfTheTimesMatch_andContinuesIncrementingPlacingAfterwards()
    {
        $now = Carbon::now();

        $race = new RaceModel();
        $race->setShortName('Foo');
        $race->setName('Foo');
        $race->setLength(1500);

        $horse1 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $horse2 = $this->createSlowestPossibleHorseWithGivenBaseSpeed(2);
        $slowestHorse = $this->createSlowestPossibleHorseWithGivenBaseSpeed(3);
        $this->enterHorsesIntoRace($now->copy(), $race, $horse1, $horse2, $slowestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish()), [$race], 3);
        $responseData = $response->getData(true);

        $this->assertEquals(2, $responseData['races'][0]['horses'][2]['position']);
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
        $fastestHorse = $this->createFastestPossibleHorseWithGivenBaseSpeed(1);
        $this->enterHorsesIntoRace($now, $race, $slowestHorse, $fastestHorse);

        $response = new RaceIndexResponse($now->copy()->addSeconds($slowestHorse->getPerformance()->getSecondsToFinish() - 1), [$race], 2);
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

    protected function enterHorsesIntoRace(Carbon $now, RaceModel $race, HorseModel ...$horses)
    {
        $timesTakenToFinish = [];
        foreach ($horses as $horse) {
            $performance = new RaceHorsePerformanceModel();
            $secondsTakenToCompleteRace = $timesTakenToFinish[] = $horse->calculateSecondsToRunGivenDistance($race->getLength());
            $performance->setSecondsToFinish($secondsTakenToCompleteRace);
            $horse->setRelation('pivot', $performance);
        }
        $race->setRelation('horses', new HorseModelCollection($horses));
        $race->setFinishedAt($now->addSeconds(max($timesTakenToFinish)));
    }
}
