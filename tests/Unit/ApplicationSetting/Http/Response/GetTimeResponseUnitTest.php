<?php

namespace Tests\Unit\Http\Response;

use App\ApplicationSetting\Exception\NegativeTimeException;
use App\ApplicationSetting\Http\Etag;
use App\ApplicationSetting\Http\Response\GetApplicationTimeResponse;
use Illuminate\Http\JsonResponse;
use Tests\Unit\UnitTestCase;
use TypeError;

class GetTimeResponseUnitTest extends UnitTestCase
{
    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [1]
     *           [0]
     */
    public function construct__itAcceptsAPositiveInteger($time)
    {
        $this->expectNotToPerformAssertions();
        new GetApplicationTimeResponse($time);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [1]
     */
    public function construct__itReturnsAnInstanceOfJsonResponse($time)
    {
        $this->assertInstanceOf(JsonResponse::class, new GetApplicationTimeResponse($time));
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
     * @testWith [-1]
     */
    public function construct__itThrowsAnExceptionWhenNotProvidedPositiveInteger($time)
    {
        $this->expectException(NegativeTimeException::class);
        new GetApplicationTimeResponse($time);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsATimeEtagHeader($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertEquals($response->getEtag(), sprintf('"%s"', Etag::TIME));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertIsArray($response->getData(true));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertArrayHasKey("time", $response->getData(true));
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeArray($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertIsArray($response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingACurrentKey($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertArrayHasKey("current", $response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::__construct
     * @testWith [7]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingATime($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertEquals(7, $response->getData(true)['time']['current']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::getApplicationTime
     */
    public function getApplicationTime__itIsCallable()
    {
        $response = new GetApplicationTimeResponse(0);
        $this->assertIsCallable([$response, 'getApplicationTime']);
    }

    /**
     * @test
     * @covers GetApplicationTimeResponse::getApplicationTime
     * @testWith [0]
     */
    public function getApplicationTime__itGetsTheApplicationTimeSet($time)
    {
        $response = new GetApplicationTimeResponse($time);
        $this->assertEquals($time, $response->getApplicationTime());
    }
}
