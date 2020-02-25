<?php

namespace Tests\Unit\Race\Http\Controller;

use App\Http\Response\Error\ErrorBuilder;
use Tests\Unit\CreatesControllersTrait;
use Tests\Unit\UnitTestCase;
use App\Race\Http\Controller\RaceController;

class RaceControllerTest extends UnitTestCase
{
    use CreatesControllersTrait;

    /**
     * @test
     * @covers RaceController::store
     */
    public function store__isCallable()
    {
        $errorBuilder = $this->getMockBuilder(ErrorBuilder::class)->disableOriginalConstructor()->getMock();
        $controller = $this->createController(RaceController::class, null, $errorBuilder);
        $this->assertIsCallable([$controller, 'store']);
    }
}
