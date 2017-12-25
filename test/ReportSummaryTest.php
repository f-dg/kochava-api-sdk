<?php

namespace KochavaReportingTest;

use \KochavaReporting\ReportSummary,
    \KochavaReporting\AbstractReport,
    \KochavaReporting\CurlAdapter,
    \KochavaReporting\ReportSummaryValues;

class ReportSummaryTest extends \PHPUnit\Framework\TestCase
{
    const TEST_TOKEN = 'token_string';
    const TEST_ADVERTIZER_ID = '123456';

    public $reportSummary;

    protected function setUp()
    {
        $this->reportSummary = $this->getMockBuilder(ReportSummary::class)
            ->setConstructorArgs([
                $this->getMockedCurlAdapter(),
                static::TEST_TOKEN,
                static::TEST_ADVERTIZER_ID])
            ->setMethods(['getEndpoint'])
            ->getMock();

        $endpoint = AbstractReport::BASE_URL 
                  . '/' . AbstractReport::DEFAULT_API_VERSION 
                  . '/' . ReportSummary::ENDPOINT;

        $this->reportSummary->expects($this->any())
                            ->method('getEndpoint')
                            ->with($this->stringContains(ReportSummary::ENDPOINT))
                            ->will($this->returnValue($endpoint));
    }

    protected function tearDown()
    {
        $this->reportSummary = null;
    }

    public function testAddReportToQueue()
    {
        $method = $this->getMethod('addReportToQueue');

        $validParams = [
            ReportSummaryValues::FIELD_TIME_SIRIES => '1',
            ReportSummaryValues::FIELD_DELIVERY_FORMAT => ReportSummary::DELIVERY_FORMAT_CSV,
        ];

        $method->invoke($this->reportSummary, $validParams);
        $this->assertEquals(
            ReportSummary::DELIVERY_FORMAT_CSV, 
            $this->reportSummary->getDeliveryFormat()
        );

        $notValidParams = [
            ReportSummaryValues::FIELD_DELIVERY_FORMAT => ReportSummary::DELIVERY_FORMAT_CSV,
        ];
        $this->expectException(\KochavaReporting\ReportSummaryException::class);
        $method->invoke($this->reportSummary, $notValidParams);
    }

    public function testGetDefaultParams()
    {
        $method = $this->getMethod('getDefaultParams');
        $params = $method->invoke($this->reportSummary);

        $this->assertArrayHasKey(ReportSummaryValues::FIELD_API_KEY, $params);
        $this->assertArrayHasKey(ReportSummaryValues::FIELD_APP_GUID, $params);
        $this->assertArrayHasKey(ReportSummaryValues::FIELD_TRAFFIC, $params);
        $this->assertArrayHasKey(ReportSummaryValues::FIELD_DELIVERY_FORMAT, $params);

        $this->assertEquals(
            static::TEST_TOKEN, 
            $params[ReportSummaryValues::FIELD_API_KEY]
        );
        $this->assertEquals(
            static::TEST_ADVERTIZER_ID, 
            $params[ReportSummaryValues::FIELD_APP_GUID]
        );
    }

    protected function getMethod($name)
    {
        $class = new \ReflectionClass($this->reportSummary);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function getMockedCurlAdapter()
    {
        $mockedCurl = $this->getMockBuilder(CurlAdapter::class)
                    ->setMethods(['postRequest'])
                    ->getMock();

        $mockedCurl->expects($this->any())
                   ->method('postRequest')
                   ->with(
                       $this->stringEndsWith(ReportSummary::ENDPOINT),
                       $this->logicalAnd(
                           $this->arrayHasKey(ReportSummaryValues::FIELD_TIME_SIRIES),
                           $this->arrayHasKey(ReportSummaryValues::FIELD_API_KEY),
                           $this->arrayHasKey(ReportSummaryValues::FIELD_APP_GUID),
                           $this->arrayHasKey(ReportSummaryValues::FIELD_TRAFFIC)
                       ))
                   ->will($this->returnValue(['status' => 'completed']));
        return $mockedCurl;
    }
}

