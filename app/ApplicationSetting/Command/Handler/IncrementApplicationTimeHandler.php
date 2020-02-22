<?php

namespace App\ApplicationSetting\Command\Handler;

use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\FailedToIncrementTimeException;
use App\Lib\Command\Command;
use App\Lib\Command\HandlerInterface;

/**
 * @see IncrementApplicationTimeCommand
 */
class IncrementApplicationTimeHandler implements HandlerInterface
{
    /**
     * @var ApplicationSettingRepository
     */
    protected $settingRepository;

    /**
     * @param ApplicationSettingRepository $settingRepository
     */
    public function __construct(ApplicationSettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param Command|IncrementApplicationTimeCommand $command
     * @throws ApplicationTimeDoesNotMatchObservedTimeException
     * @throws FailedToIncrementTimeException
     */
    public function handle($command)
    {
        $query = $this->settingRepository->newQueryBuilder()->where('id', ApplicationSettingName::TIME);
        $observedTime = $command->getObservedTime();
        if (null !== $observedTime) {
            $query = $query->where('value', $observedTime);
        }
        if ($query->increment('value', $command->getSecondsToIncrementBy()) < 1) {
            if (null !== $observedTime) {
                throw new ApplicationTimeDoesNotMatchObservedTimeException();
            }
            throw new FailedToIncrementTimeException();
        }
    }
}
