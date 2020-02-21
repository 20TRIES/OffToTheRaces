<?php

namespace Tests\Unit\Http\Response;

use App\ApplicationSetting\Exception\NegativeTimeException;
use App\ApplicationSetting\Http\Etag;
use App\ApplicationSetting\Http\Response\GetTimeResponse;
use Illuminate\Http\JsonResponse;
use Tests\Unit\TestCase;
use TypeError;

class GetTimeResponseTest extends TestCase
{
    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [1]
     *           [0]
     */
    public function construct__itAcceptsPositiveInteger($time)
    {
        $this->expectNotToPerformAssertions();
        new GetTimeResponse($time);
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [1]
     */
    public function construct__itReturnsInstanceOfJsonResponse($time)
    {
        $this->assertInstanceOf(JsonResponse::class, new GetTimeResponse($time));
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith ["foo"]
     */
    public function construct__itThrowsTypeErrorWhenNotProvidedInteger($time)
    {
        $this->expectException(TypeError::class);
        new GetTimeResponse($time);
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [-1]
     */
    public function construct__itThrowsExceptionWhenNotProvidedPositiveInteger($time)
    {
        $this->expectException(NegativeTimeException::class);
        new GetTimeResponse($time);
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsATimeEtagHeader($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertEquals($response->getEtag(), sprintf('"%s"', Etag::TIME));
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertIsArray($response->getData(true));
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertArrayHasKey("time", $response->getData(true));
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeArray($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertIsArray($response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [0]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingACurrentKey($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertArrayHasKey("current", $response->getData(true)['time']);
    }

    /**
     * @test
     * @covers GetTimeResponse::__construct
     * @testWith [7]
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingATime($time)
    {
        $response = new GetTimeResponse($time);
        $this->assertEquals(7, $response->getData(true)['time']['current']);
    }
}
