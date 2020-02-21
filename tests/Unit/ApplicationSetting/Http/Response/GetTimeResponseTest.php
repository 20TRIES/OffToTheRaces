<?php

namespace Tests\Unit\Http\Response;

use App\ApplicationSetting\Exception\NegativeTimeException;
use App\ApplicationSetting\Http\Response\GetTimeResponse;
use Illuminate\Http\JsonResponse;
use Tests\Unit\TestCase;
use TypeError;

class GetTimeResponseTest extends TestCase
{
    /**
     * @test
     */
    public function construct__itAcceptsPositiveInteger()
    {
        $this->expectNotToPerformAssertions();
        new \App\ApplicationSetting\Http\Response\GetTimeResponse(1);
    }

    /**
     * @test
     */
    public function construct__itReturnsInstanceOfJsonResponse()
    {
        $this->assertInstanceOf(JsonResponse::class, new \App\ApplicationSetting\Http\Response\GetTimeResponse(1));
    }

    /**
     * @test
     */
    public function construct__itThrowsExceptionWhenNotProvidedInteger()
    {
        $this->expectException(TypeError::class);
        new \App\ApplicationSetting\Http\Response\GetTimeResponse('foo');
    }

    /**
     * @test
     */
    public function construct__itAcceptsAZeroTime()
    {
        $this->expectNotToPerformAssertions();
        new GetTimeResponse(0);
    }

    /**
     * @test
     */
    public function construct__itThrowsExceptionIfProvidedNegativeInteger()
    {
        $this->expectException(NegativeTimeException::class);
        new GetTimeResponse(-1);
    }

    /**
     * @test
     */
    public function construct__itSetsATimeEtagHeader()
    {
        $response = new \App\ApplicationSetting\Http\Response\GetTimeResponse(0);
        $this->assertEquals($response->getEtag(), sprintf('"%s"', GetTimeResponse::ETAG_TIME));
    }

    /**
     * @test
     */
    public function construct__itSetsAnArrayOfData()
    {
        $response = new \App\ApplicationSetting\Http\Response\GetTimeResponse(0);
        $this->assertIsArray($response->getData(true));
    }

    /**
     * @test
     */
    public function construct__itSetsAnArrayOfData_withATimeKey()
    {
        $response = new \App\ApplicationSetting\Http\Response\GetTimeResponse(0);
        $this->assertArrayHasKey("time", $response->getData(true));
    }

    /**
     * @test
     */
    public function construct__itSetsAnArrayOfData_withATimeArray()
    {
        $response = new \App\ApplicationSetting\Http\Response\GetTimeResponse(0);
        $this->assertIsArray($response->getData(true)['time']);
    }

    /**
     * @test
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingACurrentKey()
    {
        $response = new \App\ApplicationSetting\Http\Response\GetTimeResponse(0);
        $this->assertArrayHasKey("current", $response->getData(true)['time']);
    }

    /**
     * @test
     */
    public function construct__itSetsAnArrayOfData_withATimeKey_containingATime()
    {
        $response = new GetTimeResponse(7);
        $this->assertEquals(7, $response->getData(true)['time']['current']);
    }
}
