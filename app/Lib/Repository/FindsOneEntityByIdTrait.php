<?php

namespace App\Lib\Repository;

use App\ApplicationSetting\ApplicationSettingModel;
use Illuminate\Database\Eloquent\Model;

trait FindsOneEntityByIdTrait
{
    /**
     * Finds a model by id.
     *
     * @param mixed $id
     * @return ApplicationSettingModel
     */
    public function findOneById($id): Model
    {
        return static::newQueryBuilder()->where('id', $id)->first();
    }
}
