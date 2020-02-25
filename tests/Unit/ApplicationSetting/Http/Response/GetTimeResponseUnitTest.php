<?php

namespace Tests\Unit\Http\Response;

use App\ApplicationSetting\Http\Etag;
use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use App\Lib\DateTime\Format;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Tests\Unit\UnitTestCase;
use TypeError;

class GetTimeResponseUnitTest extends UnitTestCase
{
    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itAcceptsACarbonInstance()
    {
        $now = Carbon::now();
        $this->expectNotToPerformAssertions();
        new GetApplicationTimeResponse($now);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itReturnsAnInstanceOfJsonResponse()
    {
        $now = Carbon::now();
        $this->assertInstanceOf(JsonResponse::class, new GetApplicationTimeResponse($now));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith ["foo"]
     */
    public function construct__itThrowsATypeErrorWhenNotProvidedInteger($time)
    {
        $this->expectException(TypeError::class);
        new GetApplicationTimeResponse($time);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itSetsATimeEtagHeader()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertEquals($response->getEtag(), sprintf('"%s"', Etag::TIME));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itSetsAnArrayOfData()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertIsArray($response->getData(true));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itSetsAnArrayOfData_withATimeKey()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertArrayHasKey("time", $response->getData(true));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itSetsAnArrayOfData_withATimeArray()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertIsArray($response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingACurrentKey()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertArrayHasKey("current", $response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingATime()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertEquals($now->format(Format::DEFAULT), $response->getData(true)['time']['current']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::getApplicationTime
     */
    public function getApplicationTime__itIsCallable()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertIsCallable([$response, 'getApplicationTime']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::getApplicationTime
     * @testWith [0]
     */
    public function getApplicationTime__itGetsTheApplicationTimeSet()
    {
        $now = Carbon::now();
        $response = new GetApplicationTimeResponse($now);
        $this->assertEquals($now->format(Format::DEFAULT), $response->getApplicationTime());
    }
}
