<?php

namespace App\Race;

use App\Lib\Repository\Exception\ModelTimestampsDisabledException;
use App\Lib\Repository\FindsOneEntityByIdTrait;
use App\Lib\Repository\FindsOneEntityByIdInterface;
use App\Lib\Repository\GetsLastNEntitiesInterface;
use App\Lib\Repository\GetsLastNEntitiesTrait;
use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RaceRepository extends Repository implements FindsOneEntityByIdInterface, PersistsEntitiesInterface, GetsLastNEntitiesInterface
{
    use FindsOneEntityByIdTrait;
    use PersistsEntitiesTrait;
    use GetsLastNEntitiesTrait;

    /**
     * @return string
     */
    public function getModelClassReference(): string
    {
        return RaceModel::class;
    }

    /**
     * Gets a query builder for a query to get any races that end after a given time.
     *
     * @param Builder $query
     * @param Carbon $time
     * @return Builder
     */
    protected function filterQueryToRacesThatEndAfter(Builder $query, Carbon $time): Builder
    {
        return $query->where(function (Builder $query) use ($time) {
            return $query->where(RaceModel::ATTRIBUTE_FINISHED_AT, '>', $time)
                ->orWhereNull(RaceModel::ATTRIBUTE_FINISHED_AT);
        });
    }

    /**
     * Gets a query builder for a query to get any races that end after a given time.
     *
     * @param Builder $query
     * @param Carbon $time
     * @return Builder
     */
    protected function filterQueryToRacesThatEndOnOrBefore(Builder $query, Carbon $time): Builder
    {
        return $query->where(function (Builder $query) use ($time) {
            return $query->where(RaceModel::ATTRIBUTE_FINISHED_AT, '<=', $time)
                ->whereNotNull(RaceModel::ATTRIBUTE_FINISHED_AT);
        });
    }

    /**
     * Counts the number of currently active races.
     *
     * @param Carbon $time
     * @return int
     */
    public function countRacesThatEndAfter(Carbon $time)
    {
        return $this->filterQueryToRacesThatEndAfter($this->newQueryBuilder(), $time)->count();
    }

    /**
     * Get any races that end after a given time.
     *
     * @param Carbon $time
     * @return Collection<RaceModel>
     */
    public function getRacesThatEndAfter(Carbon $time)
    {
        return $this->filterQueryToRacesThatEndAfter($this->newQueryBuilder(), $time)->get();
    }

    /**
     * Gets the last "n" races that ended before a given time.
     *
     * @param int $limit
     * @param Carbon $time
     * @return Collection
     */
    public function getLastNRacesThatEndOnOrBefore(int $limit, Carbon $time): Collection
    {
        return $this->filterQueryToRacesThatEndOnOrBefore($this->newQueryBuilder(), $time)
            ->orderBy(RaceModel::ATTRIBUTE_FINISHED_AT, 'DESC')
            ->get();
    }
}
