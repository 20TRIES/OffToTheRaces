<?php

namespace App\ApplicationSetting\Command;

use App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler;
use App\Lib\Command\Command;

/**
 * Increments the application time by a given number of seconds.
 *
 * @see \App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler
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
     */
    public function __construct(int $secondsToIncrementBy, int $observedTime = null)
    {
        // @todo test that exception is thrown if seconds is negative...
        // @todo test that exception is thrown if seconds is not an int
        // @todo test that exception is thrown if $observedTime is negative...
        // @todo test that exception is thrown if $observedTime is not an int

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
