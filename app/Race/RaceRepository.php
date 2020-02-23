<?php

namespace App\Race;

use App\Lib\Repository\FindsOneEntityByIdTrait;
use App\Lib\Repository\FindsOneEntityByIdInterface;
use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RaceRepository extends Repository implements FindsOneEntityByIdInterface, PersistsEntitiesInterface
{
    use FindsOneEntityByIdTrait;
    use PersistsEntitiesTrait;

    /**
     * @return Builder
     */
    public function newQueryBuilder(): Builder
    {
        return RaceModel::query();
    }

    /**
     * Gets a query builder for a query to get any races that end after a given time.
     *
     * @param Carbon $time
     * @return Builder
     */
    protected function newQueryBuilderForRacesThatEndAfter(Carbon $time)
    {
        return $this->newQueryBuilder()->where(RaceModel::ATTRIBUTE_FINISHED_AT, '>', $time);
    }

    /**
     * Counts the number of currently active races.
     *
     * @param Carbon $time
     * @return int
     */
    public function countRacesThatEndAfter(Carbon $time)
    {
        return $this->newQueryBuilderForRacesThatEndAfter($time)->count();
    }

    /**
     * Get any currently active races.
     *
     * @param Carbon $time
     * @return Collection<RaceModel>
     */
    public function getRacesThatEndAfter(Carbon $time)
    {
        return $this->newQueryBuilderForRacesThatEndAfter($time)->get();
    }
}
