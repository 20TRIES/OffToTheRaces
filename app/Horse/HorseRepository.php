<?php

namespace App\Horse;

use App\Lib\Repository\FindsOneEntityByIdInterface;
use App\Lib\Repository\FindsOneEntityByIdTrait;
use App\Lib\Repository\PersistsEntitiesInterface;
use App\Lib\Repository\PersistsEntitiesTrait;
use App\Lib\Repository\Repository;

class HorseRepository extends Repository implements PersistsEntitiesInterface, FindsOneEntityByIdInterface
{
    use PersistsEntitiesTrait;
    use FindsOneEntityByIdTrait;

    /**
     * @return string
     */
    public function getModelClassReference(): string
    {
        return HorseModel::class;
    }
}
