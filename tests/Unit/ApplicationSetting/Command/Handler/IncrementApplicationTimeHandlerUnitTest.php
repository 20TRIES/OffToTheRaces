<?php

namespace Tests\Unit\ApplicationSetting\Command\Handler;

use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Command\Handler\IncrementApplicationTimeHandler;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\Exception\FailedToIncrementTimeException;
use App\Lib\DateTime\Format;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Tests\Unit\UnitTestCase;

class IncrementApplicationTimeHandlerUnitTest extends UnitTestCase
{
    /**
     * @test
     * @covers IncrementApplicationTimeHandler::handle
     */
    public function handle__isCallable()
    {
        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $handler = new IncrementApplicationTimeHandler($repository);
        $this->assertIsCallable([$handler, 'handle']);
    }

    /**
     * @test
     * @testWith [10]
     * @covers IncrementApplicationTimeHandler::handle
     */
    public function handle__filtersResultsBeingUpdatedToOnlyThoseWhereTheSettingIdIsEqualToTime($secondsToIncrementBy)
    {
        $queryBuilder = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('id', ApplicationSettingName::TIME)
            ->willReturnSelf();
        $queryBuilder->expects($this->any())->method('update')->willReturn(1);

        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())->method('newQueryBuilder')->willReturn($queryBuilder);

        $command = new IncrementApplicationTimeCommand($secondsToIncrementBy);

        $handler = new IncrementApplicationTimeHandler($repository);
        $handler->handle($command);
    }

    /**
     * @test
     * @testWith [10]
     * @covers IncrementApplicationTimeHandler::handle
     *
     * @param int $secondsToIncrementBy
     * @throws ApplicationTimeDoesNotMatchObservedTimeException
     * @throws FailedToIncrementTimeException
     */
    public function handle__incrementsTime(int $secondsToIncrementBy)
    {
        $now = Carbon::now();

        $queryBuilder = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->once())
            ->method('update')
            ->with(['value' => $now->clone()->addSeconds($secondsToIncrementBy)->format(Format::DEFAULT)])
            ->willReturn(1);

        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())->method('newQueryBuilder')->willReturn($queryBuilder);

        $command = new IncrementApplicationTimeCommand($secondsToIncrementBy, $now);
        $handler = new IncrementApplicationTimeHandler($repository);
        $handler->handle($command);
    }

    /**
     * @test
     * @testWith [10]
     * @covers IncrementApplicationTimeHandler::handle
     */
    public function handle__whereProvidedAnObservedTime_filtersResultsBeingUpdatedToOnlyThoseWithThatValue(int $secondsToIncrementBy)
    {
        $now = Carbon::now();

        $queryBuilder = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->at(1))->method('where')->with('value', $now->format(Format::DEFAULT))->willReturnSelf();
        $queryBuilder->expects($this->any())->method('update')->willReturn(1);

        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())->method('newQueryBuilder')->willReturn($queryBuilder);

        $command = new IncrementApplicationTimeCommand($secondsToIncrementBy, $now);

        $handler = new IncrementApplicationTimeHandler($repository);
        $handler->handle($command);
    }

    /**
     * @test
     * @testWith [10]
     * @covers IncrementApplicationTimeHandler::handle
     */
    public function handle__whereProvidedAnObservedTime_andNumberOfResultsUpdatedIsLessThenOne_throwsApplicationTimeDoesNotMatchObservedTimeException($secondsToIncrementBy)
    {
        $now = Carbon::now();

        $queryBuilder = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('update')->willReturn(0);

        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())->method('newQueryBuilder')->willReturn($queryBuilder);

        $command = new IncrementApplicationTimeCommand($secondsToIncrementBy, $now);

        $handler = new IncrementApplicationTimeHandler($repository);
        $this->expectException(ApplicationTimeDoesNotMatchObservedTimeException::class);
        $handler->handle($command);
    }

    /**
     * @test
     * @testWith [10]
     * @covers IncrementApplicationTimeHandler::handle
     */
    public function handle__whereNotProvidedAnObservedTime_andNumberOfResultsUpdatedIsLessThenOne_throwsFailedToIncrementTimeException($secondsToIncrementBy)
    {
        $queryBuilder = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->getMock();
        $queryBuilder->expects($this->any())->method('where')->willReturnSelf();
        $queryBuilder->expects($this->any())->method('update')->willReturn(0);

        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())->method('newQueryBuilder')->willReturn($queryBuilder);

        $command = new IncrementApplicationTimeCommand($secondsToIncrementBy);

        $handler = new IncrementApplicationTimeHandler($repository);
        $this->expectException(FailedToIncrementTimeException::class);
        $handler->handle($command);
    }
}
