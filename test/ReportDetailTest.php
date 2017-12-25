<?php

namespace KochavaReportingTest;

use \KochavaReporting\ReportDetail,
    \KochavaReporting\AbstractReport,
    \KochavaReporting\CurlAdapter,
    \KochavaReporting\ReportDetailValues;

class ReportDetailTest extends \PHPUnit\Framework\TestCase
{
    const TEST_TOKEN = 'token_string';
    const TEST_ADVERTIZER_ID = '123456';

    public $reportDetail;

    protected function setUp()
    {
        $this->reportDetail = $this->getMockBuilder(ReportDetail::class)
            ->setConstructorArgs([
                $this->getMockedCurlAdapter(),
                static::TEST_TOKEN,
                static::TEST_ADVERTIZER_ID])
            ->setMethods(['getEndpoint'])
            ->getMock();

        $endpoint = AbstractReport::BASE_URL 
                  . '/' . AbstractReport::DEFAULT_API_VERSION 
                  . '/' . ReportDetail::ENDPOINT;

        $this->reportDetail->expects($this->any())
                           ->method('getEndpoint')
                           ->with($this->stringContains(ReportDetail::ENDPOINT))
                           ->will($this->returnValue($endpoint));
    }

    protected function tearDown()
    {
        $this->reportDetail = null;
    }

    public function testAddReportToQueue()
    {
        $method = $this->getMethod('addReportToQueue');

        $validParams = [
            ReportDetailValues::FIELD_DELIVERY_FORMAT => ReportDetail::DELIVERY_FORMAT_CSV,
            ReportDetailValues::FIELD_TIME_ZONE       => ReportDetail::DEFAULT_TIME_ZONE,
        ];

        $method->invoke($this->reportDetail, $validParams);
        $this->assertEquals(
            ReportDetail::DELIVERY_FORMAT_CSV, 
            $this->reportDetail->getDeliveryFormat()
        );
    }

    public function testGetDefaultParams()
    {
        $method = $this->getMethod('getDefaultParams');
        $params = $method->invoke($this->reportDetail);

        $this->assertArrayHasKey(ReportDetailValues::FIELD_API_KEY, $params);
        $this->assertArrayHasKey(ReportDetailValues::FIELD_APP_GUID, $params);
        $this->assertArrayHasKey(ReportDetailValues::FIELD_TRAFFIC, $params);
        $this->assertArrayHasKey(ReportDetailValues::FIELD_DELIVERY_FORMAT, $params);

        $this->assertEquals(
            static::TEST_TOKEN, 
            $params[ReportDetailValues::FIELD_API_KEY]
        );
        $this->assertEquals(
            static::TEST_ADVERTIZER_ID, 
            $params[ReportDetailValues::FIELD_APP_GUID]
        );
    }

    protected function getMethod($name)
    {
        $class = new \ReflectionClass($this->reportDetail);
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
                       $this->stringEndsWith(ReportDetail::ENDPOINT),
                       $this->logicalAnd(
                           $this->arrayHasKey(ReportDetailValues::FIELD_API_KEY),
                           $this->arrayHasKey(ReportDetailValues::FIELD_APP_GUID),
                           $this->arrayHasKey(ReportDetailValues::FIELD_TRAFFIC),
                           $this->arrayHasKey(ReportDetailValues::FIELD_TIME_ZONE)
                       ))
                   ->will($this->returnValue(['status' => 'completed']));
        return $mockedCurl;
    }
}

