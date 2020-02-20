<?php

namespace App\ApplicationSetting\Command\Handler;

use App\Lib\Command\Command;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\Exceptions\FailedToSaveModelException;
use App\ApplicationSetting\ApplicationSettingModel;
use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\Lib\Command\HandlerInterface;

/**
 * @see IncrementApplicationTimeCommand
 */
class IncrementApplicationTimeHandler implements HandlerInterface
{
    /**
     * @var \App\ApplicationSetting\ApplicationSettingRepository
     */
    protected $settingRepository;

    /**
     * @param \App\ApplicationSetting\ApplicationSettingRepository $settingRepository
     */
    public function __construct(ApplicationSettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param \App\Lib\Command\Command|IncrementApplicationTimeCommand $command
     * @return int
     * @throws ApplicationTimeDoesNotMatchObservedTimeException
     * @throws FailedToSaveModelException
     */
    public function handle($command)
    {
        $setting = $this->settingRepository->findOneById(ApplicationSettingName::TIME);
        $currentTime = (int) $setting->getValue();
        $observedTime = $command->getObservedTime();
        $query = ApplicationSettingModel::query()->where('id', ApplicationSettingName::TIME);
        if (null !== $observedTime) {
            $query = $query->where('value', $observedTime);
        }
        $numberOfRowsUpdated = $query->update(['value' => $updatedTime = $currentTime + $command->getSecondsToIncrementBy()]);
        if (null !== $observedTime && $numberOfRowsUpdated < 1) {
            throw new ApplicationTimeDoesNotMatchObservedTimeException();
        }
        if (true !== $setting->save()) {
            throw new FailedToSaveModelException();
        }
        $setting = $setting->fresh();
        return (int) $setting->getValue();
    }
}
