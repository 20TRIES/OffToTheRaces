<?php

namespace App\Race\Command\Handler;

use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\Horse\HorseModel;
use App\Horse\HorseRepository;
use App\Lib\Command\Command;
use App\Lib\Command\HandlerInterface;
use App\Lib\DateTime\Format;
use App\Lib\Repository\Exception\FailedToSaveEntityException;
use App\Race\Command\CreateRaceCommand;
use App\Race\Exception\InvalidRaceLengthException;
use App\Race\Exception\MaxActiveRacesAlreadyReachedException;
use App\Race\RaceModel;
use App\Performance\RaceHorsePerformanceModel;
use App\Performance\RaceHorsePerformanceRepository;
use App\Race\RaceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @see CreateRaceCommand
 */
class CreateRaceHandler implements HandlerInterface
{
    /**
     * @var int
     */
    const MAX_ACTIVE_RACES = 3;

    /**
     * @var int
     */
    const HORSES_PER_RACE = 8;

    /**
     * @var int
     */
    const DEFAULT_RACE_LENGTH_METERS = 1500;

    /**
     * @var ApplicationSettingRepository
     */
    protected $settingRepository;

    /**
     * @var RaceRepository
     */
    protected $raceRepository;

    /**
     * @var HorseRepository
     */
    protected $horseRepository;

    /**
     * @var \App\Performance\RaceHorsePerformanceRepository
     */
    protected $racePerformanceRepository;

    /**
     * @param ApplicationSettingRepository $settingRepository
     * @param RaceRepository $raceRepository
     * @param HorseRepository $horseRepository
     * @param \App\Performance\RaceHorsePerformanceRepository $racePerformanceRepository
     */
    public function __construct(
        ApplicationSettingRepository $settingRepository,
        RaceRepository $raceRepository,
        HorseRepository $horseRepository,
        RaceHorsePerformanceRepository $racePerformanceRepository
    )
    {
        $this->settingRepository = $settingRepository;
        $this->raceRepository = $raceRepository;
        $this->horseRepository = $horseRepository;
        $this->racePerformanceRepository = $racePerformanceRepository;
    }

    /**
     * @param Command|CreateRaceCommand $command
     * @return RaceModel
     * @throws ApplicationTimeNotFoundException
     * @throws InvalidRaceLengthException
     * @throws MaxActiveRacesAlreadyReachedException
     * @throws FailedToSaveEntityException
     */
    public function handle($command)
    {
        DB::beginTransaction();
        try {
            $applicationTime = $this->settingRepository->getApplicationTime();
            $numberOfActiveRaces = $this->raceRepository->countRacesThatEndAfter($applicationTime);
            if ($numberOfActiveRaces >= static::MAX_ACTIVE_RACES) {
                throw new MaxActiveRacesAlreadyReachedException();
            }
            $race = new RaceModel();
            $race->initializeShortName();
            $race->setName($command->getName() ?? sprintf('Unnamed race (%s)', $applicationTime->format(Format::DEFAULT)));
            $race->setLength($raceLength = static::DEFAULT_RACE_LENGTH_METERS);
            $horses = $this->generateHorseModels(static::HORSES_PER_RACE);
            $this->raceRepository->persist($race);
            $this->horseRepository->persist(...$horses);
            $racePerformances = $timesToFinish = [];
            foreach ($horses as $horse) {
                $racePerformances[] = $racePerformance = RaceHorsePerformanceModel::createFromHorseAndRace($horse, $race);
                $timesToFinish[] = $racePerformance->getSecondsToFinish();
            }
            $this->racePerformanceRepository->persist(...$racePerformances);
            $race->setFinishedAt($applicationTime->addSeconds(max($timesToFinish)));
            $this->raceRepository->persist($race);
            $completedSuccessfully = true;
        } finally {
            if (true === ($completedSuccessfully ?? false)) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        }
        return $race;
    }

    /**
     * Generates a given number of horse models.
     *
     * @param int $numberOfHorses
     * @return array<HorseModel>
     */
    protected function generateHorseModels(int $numberOfHorses): array
    {
        $horses = [];
        while (count($horses) < $numberOfHorses) {
            $horses[] = HorseModel::createFromDefaults();
        }
        return $horses;
    }
}
