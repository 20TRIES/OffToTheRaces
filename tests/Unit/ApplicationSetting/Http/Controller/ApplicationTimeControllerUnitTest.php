<?php

namespace Tests\Unit\ApplicationSetting\Http\Controller;

use App\ApplicationSetting\ApplicationSettingModel;
use App\ApplicationSetting\ApplicationSettingName;
use App\ApplicationSetting\ApplicationSettingRepository;
use App\ApplicationSetting\Command\IncrementApplicationTimeCommand;
use App\ApplicationSetting\Exception\ApplicationTimeDoesNotMatchObservedTimeException;
use App\ApplicationSetting\Http\Controller\ApplicationTimeController;
use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use App\Lib\Enum\Http\Request\Header;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Tactician\CommandBus;
use Tests\Unit\CreatesControllersTrait;
use Tests\Unit\UnitTestCase;

class ApplicationTimeControllerUnitTest extends UnitTestCase
{
    use CreatesControllersTrait;

    /**
     * @test
     * @covers ApplicationTimeController::index
     */
    public function index__itIsCallable()
    {
        $this->assertIsCallable([$this->createController(ApplicationTimeController::class), 'index']);
    }

    /**
     * @test
     * @covers ApplicationTimeController::index
     */
    public function index__itReturnsAGetTimeResponse()
    {
        $time = 1;
        $model = new ApplicationSettingModel();
        $model->setValue($time);
        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())
            ->method('findOneById')
            ->with(ApplicationSettingName::TIME)
            ->willReturn($model);
        $controller = $this->createController(ApplicationTimeController::class);
        $response =  $controller->index($repository);
        $this->assertInstanceOf(GetApplicationTimeResponse::class, $response);
    }

    /**
     * @test
     * @covers ApplicationTimeController::index
     */
    public function index__itReturnsAGetTimeResponse_withTheCurrentTime()
    {
        $time = 1;
        $model = new ApplicationSettingModel();
        $model->setValue($time);
        $repository = $this->getMockBuilder(ApplicationSettingRepository::class)->getMock();
        $repository->expects($this->any())
            ->method('findOneById')
            ->with(ApplicationSettingName::TIME)
            ->willReturn($model);
        $controller = $this->createController(ApplicationTimeController::class);
        $response =  $controller->index($repository);
        $this->assertEquals($time, $response->getApplicationTime());
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__itIsCallable()
    {
        $this->assertIsCallable([$this->createController(ApplicationTimeController::class), 'update']);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__itDispatchesCommand()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $commandBus->expects($this->once())->method('handle')->willReturn(1);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $controller->update($commandBus, $request);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__itDispatchesCommandToIncrementTime()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $commandBus->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(IncrementApplicationTimeCommand::class))
            ->willReturn(1);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $controller->update($commandBus, $request);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__itDispatchesCommandToIncrementTimeByTenSeconds()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (IncrementApplicationTimeCommand $command) {
                return $command->getSecondsToIncrementBy() === 10;
            }))
            ->willReturn(1);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $controller->update($commandBus, $request);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__whenProvidedPreConditionGreaterThanOne_itDispatchesCommandToIncrementTimeWithNoPreCondition()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (IncrementApplicationTimeCommand $command) {
                return $command->getObservedTime() === null;
            }))
            ->willReturn(1);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $controller->update($commandBus, $request);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     * @testWith [7]
     */
    public function update__whenProvidedPreConditionGreaterThanOne_itDispatchesCommandToIncrementTimeWithPreCondition(int $preConditionValue)
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (IncrementApplicationTimeCommand $command) use ($preConditionValue) {
                return $command->getObservedTime() === $preConditionValue;
            }))
            ->willReturn(1);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->any())->method('header')->with(Header::IF_MATCH)->willReturn($preConditionValue);
        $controller->update($commandBus, $request);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     * @testWith [0]
     *           [-1]
     */
    public function update__whenProvidedPreConditionLessThanOne_itReturnsJsonResponse(int $preConditionValue)
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->any())->method('header')->with(Header::IF_MATCH)->willReturn($preConditionValue);
        $response = $controller->update($commandBus, $request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     * @testWith [0]
     *           [-1]
     */
    public function update__whenProvidedPreConditionLessThanOne_itReturnsPreconditionFailedHttpStatusCode(int $preConditionValue)
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->expects($this->any())->method('header')->with(Header::IF_MATCH)->willReturn($preConditionValue);
        $response = $controller->update($commandBus, $request);
        $this->assertSame(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__whenSuccessful_itReturnsJsonResponse()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $controller->update($commandBus, $request);
        $this->assertInstanceOf(GetApplicationTimeResponse::class, $response);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__whenSuccessful_itReturnsCallToIndex()
    {
        $container = $this->getMockBuilder(ContainerContract::class)->getMock();
        $controller = $this->createController($container);
        $container->expects($this->atLeastOnce())
            ->method('call')
            ->with([$controller, 'index'], [])
            ->willReturn($expectedResult = new GetApplicationTimeResponse(1));
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $controller->update($commandBus, $request);
        $this->assertSame($expectedResult, $response);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__whenPreconditionFails_itReturnsJsonResponse()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $preConditionException = new ApplicationTimeDoesNotMatchObservedTimeException();
        $commandBus->method('handle')->willThrowException($preConditionException);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $controller->update($commandBus, $request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @test
     * @covers ApplicationTimeController::update
     */
    public function update__whenPreconditionFails_itReturnsPreconditionFailedHttpStatusCode()
    {
        $controller = $this->createController(ApplicationTimeController::class);
        $commandBus = $this->getMockBuilder(CommandBus::class)->disableOriginalConstructor()->getMock();
        $preConditionException = new ApplicationTimeDoesNotMatchObservedTimeException();
        $commandBus->method('handle')->willThrowException($preConditionException);
        $request = $this->getMockBuilder(Request::class)->getMock();
        $response = $controller->update($commandBus, $request);
        $this->assertSame(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
    }
}
