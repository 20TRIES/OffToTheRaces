<?php

namespace App\ApplicationSetting\Command\Handler;

use App\ApplicationSetting\ApplicationSettingModel;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Exception\FailedToIncrementTimeException;
use App\Lib\Command\Command;
use App\Lib\Command\HandlerInterface;
use App\Lib\DateTime\Format;
use Illuminate\Database\Query\Expression;

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
        $secondsToAdd = $command->getSecondsToIncrementBy();
        if (null !== $observedTime) {
            $query = $query->where('value', $observedTime->format(Format::DEFAULT));
            $updatedValue = $observedTime->clone()->addSeconds($secondsToAdd)->format(Format::DEFAULT);
        } else {
            $updatedValue = new Expression(sprintf(
                'DATE_ADD(`%s`, INTERVAL %u SECOND)',
                ApplicationSettingModel::ATTRIBUTE_VALUE,
                $secondsToAdd
            ));
        }
        if ($query->update(['value' => $updatedValue]) < 1) {
            if (null !== $observedTime) {
                throw new ApplicationTimeDoesNotMatchObservedTimeException();
            }
            throw new FailedToIncrementTimeException();
        }
    }
}
