<?php

namespace App\Race;

use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;

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
}
