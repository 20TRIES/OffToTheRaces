<?php

namespace App\Race;

use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;
use Illuminate\Database\Eloquent\Builder;

class RacePerformanceRepository extends Repository implements PersistsEntitiesInterface
{
    use PersistsEntitiesTrait;

    /**
     * @return Builder
     */
    public function newQueryBuilder(): Builder
    {
        return RacePerformanceModel::query();
    }
}
