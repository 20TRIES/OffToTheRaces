<?php

namespace App\ApplicationSetting;

use App\ApplicationSetting\ApplicationSettingModel;
use App\Lib\Repository\FindsOneModelByIdInterface;
use App\Lib\Repository\Repository;
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
