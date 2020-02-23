<?php

namespace Tests\Unit\Race\Http\Controller;

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
        $controller = $this->createController(RaceController::class);
        $this->assertIsCallable([$controller, 'store']);
    }
}
