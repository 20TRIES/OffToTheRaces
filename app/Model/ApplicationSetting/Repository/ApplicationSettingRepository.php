<?php

namespace App\Model\ApplicationSetting\Repository;

use App\Model\ApplicationSetting\ApplicationSettingModel;
use App\Model\FindsOneModelByIdInterface;
use App\Model\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ApplicationSettingRepository extends Repository implements FindsOneModelByIdInterface
{
    /**
     * @return Builder
     */
    static function newQueryBuilder(): Builder
    {
        return ApplicationSettingModel::query();
    }

    /**
     * @param mixed $id
     * @return ApplicationSettingModel
     */
    public function findOneById($id): Model
    {
        return static::newQueryBuilder()->where('id', $id)->first();
    }
}
