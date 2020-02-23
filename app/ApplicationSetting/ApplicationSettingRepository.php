<?php

namespace App\ApplicationSetting;

use App\ApplicationSetting\Exception\ApplicationTimeNotFoundException;
use App\Lib\Repository\FindsOneEntityByIdTrait;
use App\Lib\Repository\FindsOneEntityByIdInterface;
use App\Lib\Repository\Repository;
use Carbon\Carbon;

class ApplicationSettingRepository extends Repository implements FindsOneEntityByIdInterface
{
    use FindsOneEntityByIdTrait;

    /**
     * @return string
     */
    public function getModelClassReference(): string
    {
        return ApplicationSettingModel::class;
    }

    /**
     * Gets the current application time.
     *
     * @return Carbon
     * @throws ApplicationTimeNotFoundException
     */
    public function getApplicationTime()
    {
        $setting = $this->findOneById(ApplicationSettingName::TIME);
        if (null === $setting) {
            throw new ApplicationTimeNotFoundException();
        }
        return Carbon::createFromTimestamp($setting->getValue());
    }
}
