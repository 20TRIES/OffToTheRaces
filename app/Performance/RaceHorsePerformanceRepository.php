<?php

namespace App\Performance;

use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;
use App\Performance\RaceHorsePerformanceModel;
use App\Race\RaceModel;
use App\Race\RaceRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class RaceHorsePerformanceRepository extends Repository implements PersistsEntitiesInterface
{
    use PersistsEntitiesTrait;

    /**
     * @return string
     */
    public function getModelClassReference(): string
    {
        return RaceHorsePerformanceModel::class;
    }

    /**
     * Gets the fastest performances in races finishing on or before a given time and of which are of a given length.
     *
     * @param int $length
     * @param Carbon $time
     * @param int $limit
     * @return Collection
     */
    public function getFastestNPerformancesInRacesOfGivenLengthAndCompletedOnOrBeforeGivenTime(int $length, Carbon $time, int $limit)
    {
        $query = $this->newQueryBuilder();
        $query = $query->select(sprintf("%s.*", RaceHorsePerformanceModel::TABLE));
        $query = $query->join(
            RaceModel::TABLE,
            sprintf("%s.%s", RaceHorsePerformanceModel::TABLE, RaceHorsePerformanceModel::ATTRIBUTE_RACE_ID),
            '=',
            sprintf("%s.%s", RaceModel::TABLE, RaceModel::ATTRIBUTE_ID)
        );
        $query = RaceRepository::filterQueryToRacesThatEndOnOrBefore($query, $time);
        $query = RaceRepository::filterQueryToRacesOfLength($query, $length);
        $query = $query->orderBy(RaceHorsePerformanceModel::ATTRIBUTE_TIME_TO_FINISH, 'ASC');
        $query = $query->limit($limit);
        return $query->get();
    }
}
