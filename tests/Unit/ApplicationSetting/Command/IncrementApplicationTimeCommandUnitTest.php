<?php

namespace Tests\Unit\ApplicationSetting\Command;

use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\Exception\NegativeTimeException;
use Tests\Unit\UnitTestCase;

class IncrementApplicationTimeCommandUnitTest extends UnitTestCase
{
    /**
     * @test
     * @covers IncrementApplicationTimeCommand::__construct
     * @testWith [0]
     *           [1]
     */
    public function construct__itAcceptsPositiveSecondsToIncrement($secondsToIncrement)
    {
        $this->expectNotToPerformAssertions();
        new IncrementApplicationTimeCommand($secondsToIncrement);
    }

    /**
     * @test
     * @covers IncrementApplicationTimeCommand::__construct
     * @testWith [-1]
     */
    public function construct__itThrowsExceptionIfSecondsToIncrementIsNegative($secondsToIncrement)
    {
        $this->expectException(NegativeTimeException::class);
        new IncrementApplicationTimeCommand($secondsToIncrement);
    }

    /**
     * @test
     * @covers IncrementApplicationTimeCommand::__construct
     * @testWith ["foo"]
     */
    public function construct__itThrowsTypeErrorIfSecondsToIncrementIsNotAnInteger($secondsToIncrement)
    {
        $this->expectException(\TypeError::class);
        new IncrementApplicationTimeCommand($secondsToIncrement);
    }

    /**
     * @test
     * @covers IncrementApplicationTimeCommand::__construct
     * @testWith [-1]
     */
    public function construct__itThrowsExceptionIfObservedTimeIsNegative($observedTime)
    {
        $this->expectException(NegativeTimeException::class);
        new IncrementApplicationTimeCommand(1, $observedTime);
    }

    /**
     * @test
     * @covers IncrementApplicationTimeCommand::__construct
     * @testWith ["foo"]
     */
    public function construct__itThrowsTypeErrorIfObservedTimeIsNotAnInteger($observedTime)
    {
        $this->expectException(\TypeError::class);
        new IncrementApplicationTimeCommand(1, $observedTime);
    }
}
