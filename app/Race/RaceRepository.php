<?php

namespace App\Race;

use App\Lib\Repository\FindsOneEntityByIdTrait;
use App\Lib\Repository\FindsOneEntityByIdInterface;
use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
     * Counts the number of currently active races.
     *
     * @param Carbon $time
     * @return int
     */
    public function countRacesThatEndAfter(Carbon $time)
    {
        return $this->newQueryBuilder()->where('finished_at', '>', $time)->count();
    }
}
