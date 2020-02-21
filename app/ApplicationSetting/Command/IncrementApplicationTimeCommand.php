<?php

namespace App\ApplicationSetting\Command;

use App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler;
use App\ApplicationSetting\Exception\NegativeTimeException;
use App\Lib\Command\Command;

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
     * @var int|null
     */
    private $observedTime;

    /**
     * Constructor.
     *
     * @param int $secondsToIncrementBy
     * @param int $observedTime [$observedTime=null]
     * @throws NegativeTimeException
     */
    public function __construct(int $secondsToIncrementBy, int $observedTime = null)
    {
        if ($secondsToIncrementBy < 0) {
            throw new NegativeTimeException();
        }
        if ($observedTime < 0) {
            throw new NegativeTimeException();
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
     * @return int
     */
    public function getObservedTime(): ?int
    {
        return $this->observedTime;
    }
}
