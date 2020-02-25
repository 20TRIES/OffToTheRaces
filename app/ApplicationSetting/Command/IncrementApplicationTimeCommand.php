<?php

namespace App\ApplicationSetting\Command;

use App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler;
use App\ApplicationSetting\Exception\InvalidNumberOfSecondsException;
use App\Lib\Command\Command;
use Carbon\Carbon;

/**
 * Increments the application time by a given number of seconds.
 *
 * @see IncrementApplicationTimeHandler
 */
class IncrementApplicationTimeCommand extends Command
{
    /**
     * @var int
     */
    private $secondsToIncrementBy;

    /**
     * @var Carbon|null
     */
    private $observedTime;

    /**
     * Constructor.
     *
     * @param int $secondsToIncrementBy
     * @param Carbon $observedTime [$observedTime=null]
     * @throws InvalidNumberOfSecondsException
     */
    public function __construct(int $secondsToIncrementBy, Carbon $observedTime = null)
    {
        if ($secondsToIncrementBy < 0) {
            throw new InvalidNumberOfSecondsException();
        }
        $this->secondsToIncrementBy = $secondsToIncrementBy;
        $this->observedTime = $observedTime;
    }

    /**
     * @return int
     */
    public function getSecondsToIncrementBy(): int
    {
        return $this->secondsToIncrementBy;
    }

    /**
     * @return Carbon
     */
    public function getObservedTime(): ?Carbon
    {
        return $this->observedTime;
    }
}
