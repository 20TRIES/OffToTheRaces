<?php

namespace App\ApplicationSetting;

use App\Lib\Repository\FindsModelsByIdTrait;
use App\Lib\Repository\FindsOneModelByIdInterface;
use App\Lib\Repository\Repository;
use Illuminate\Database\Eloquent\Builder;

class ApplicationSettingRepository extends Repository implements FindsOneModelByIdInterface
{
    use FindsModelsByIdTrait;

    /**
     * @return Builder
     */
    public function newQueryBuilder(): Builder
    {
        return ApplicationSettingModel::query();
    }
}
