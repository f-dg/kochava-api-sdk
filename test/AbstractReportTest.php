<?php

namespace KochavaReportingTest;

use \KochavaReporting\AbstractReport,
    \KochavaReporting\ReportSummary,
    \KochavaReporting\ReportSummaryValues,
    \KochavaReporting\AbstractReportValues,
    \KochavaReporting\CurlAdapter;

class AbstractReportTest extends \PHPUnit\Framework\TestCase
{
    const TEST_TOKEN = 'token_string';
    const TEST_ADVERTIZER_ID = '123456';

    protected $report,
              $curl;

    protected function setUp()
    {
        $this->report = $this->getMockedReport(['getEndpoint']);
    }

    protected function tearDown()
    {
        $this->report = null;
        $this->mockedCurl = null;
    }

    public function testGetApplication()
    {
        $apps = ['app1'];

        $this->mockedCurl
            ->expects($this->any())
            ->method('postRequest')
            ->with(
               $this->stringEndsWith(AbstractReport::ENDPOINT_APPLICATIONS),
               $this->arrayHasKey(AbstractReportValues::FIELD_API_KEY)
            )
            ->will($this->returnValue($apps));

        $endpoint = AbstractReport::BASE_URL 
                  . '/' . AbstractReport::DEFAULT_API_VERSION 
                  . '/' . AbstractReport::ENDPOINT_APPLICATIONS;

        $this->report->expects($this->any())
                     ->method('getEndpoint')
                     ->with($this->stringEndsWith(AbstractReport::ENDPOINT_APPLICATIONS))
                     ->will($this->returnValue($endpoint));

        $this->assertContains($apps[0], $this->report->getApplications());
    }

    public function testGetColumns()
    {
        $columns = ['col1'];

        $this->mockedCurl
            ->expects($this->any())
            ->method('postRequest')
            ->with(
               $this->stringEndsWith(AbstractReport::ENDPOINT_REPORTCOLUMNS),
               $this->logicalAnd(
                   $this->arrayHasKey(AbstractReportValues::FIELD_API_KEY),
                   $this->arrayHasKey(AbstractReportValues::FIELD_APP_GUID)
               )
            )
            ->will($this->returnValue($columns));

        $endpoint = AbstractReport::BASE_URL 
                  . '/' . AbstractReport::DEFAULT_API_VERSION 
                  . '/' . AbstractReport::ENDPOINT_REPORTCOLUMNS;

        $this->report->expects($this->once())
                     ->method('getEndpoint')
                     ->with($this->stringEndsWith(AbstractReport::ENDPOINT_REPORTCOLUMNS))
                     ->will($this->returnValue($endpoint));

        $this->assertContains($columns[0], $this->report->getColumns());
    }

    public function testGetEndpoint()
    {
        $this->report = $this->getMockedReport();
        $method = $this->getMethod('getEndpoint');

        $this->assertStringEndsWith(
            AbstractReport::ENDPOINT_REPORT_STATUS,
            $method->invoke($this->report, AbstractReport::ENDPOINT_REPORT_STATUS)
        );
    }

    public function testGetReport()
    {
        $this->report = $this->getMockedReport([
            'obtainReportStatus', 
            'fetchReport',
        ]);

        $params = [
            ReportSummaryValues::FIELD_TRAFFIC            => ['event'],
            ReportSummaryValues::FIELD_TIME_SIRIES        => '1',
        ];

        $returnFromAddReportToQueque = [
            'status' => 'queued',
        ];

        $returnFromObtainReportStatus = [
            'status' => 'completed',
            'report' => 'link',
        ];

        $report = [
            'fetchedReport1',
            'fetchedReport2',
        ];

        $this->report->expects($this->once())
                     ->method('addReportToQueue')
                     ->with($params)
                     ->will($this->returnValue($returnFromAddReportToQueque));

        $this->report->expects($this->once())
                     ->method('obtainReportStatus')
                     ->with($returnFromAddReportToQueque)
                     ->will($this->returnValue($returnFromObtainReportStatus));

        $this->report->expects($this->once())
                     ->method('fetchReport')
                     ->with($returnFromObtainReportStatus['report'])
                     ->will($this->returnValue($report));

        $this->assertContains(
            $report[0],
            $this->report->getReport($params)
        );
    }

    public function testGetReportFail()
    {
        $this->report = $this->getMockedReport([
            'obtainReportStatus', 
            'fetchReport',
        ]);

        $params = [
            ReportSummaryValues::FIELD_TRAFFIC            => ['event'],
            ReportSummaryValues::FIELD_TIME_SIRIES        => '1',
        ];

        $returnFromAddReportToQueque = [
            'status' => 'Error',
        ];

        $returnFromObtainReportStatus = [
            'status' => 'completed',
            'report' => 'link',
        ];

        $report = [
            'fetchedReport1',
            'fetchedReport2',
        ];

        $this->report->expects($this->once())
                     ->method('addReportToQueue')
                     ->with($params)
                     ->will($this->returnValue($returnFromAddReportToQueque));

        $this->expectException(\KochavaReporting\ReportException::class);
        $this->report->getReport($params);
    }

    public function testGetReportFail2()
    {
        $this->report = $this->getMockedReport([
            'obtainReportStatus', 
            'fetchReport',
        ]);

        $params = [
            ReportSummaryValues::FIELD_TRAFFIC            => ['event'],
            ReportSummaryValues::FIELD_TIME_SIRIES        => '1',
        ];

        $returnFromAddReportToQueque = [
            'status' => 'queued',
        ];

        $returnFromObtainReportStatus = [
            'status' => 'error',
        ];

        $report = [
            'fetchedReport1',
            'fetchedReport2',
        ];

        $this->report->expects($this->once())
                     ->method('addReportToQueue')
                     ->with($params)
                     ->will($this->returnValue($returnFromAddReportToQueque));

        $this->report->expects($this->once())
                     ->method('obtainReportStatus')
                     ->with($returnFromAddReportToQueque)
                     ->will($this->returnValue($returnFromObtainReportStatus));

        $this->expectException(\KochavaReporting\ReportException::class);
        $this->report->getReport($params);
    }

    public function testObtainReportStatus()
    {
        $this->report = $this->getMockedReport(['getEndpoint']);
        $method = $this->getMethod('obtainReportStatus');

        $endpoint = AbstractReport::BASE_URL 
                  . '/' . AbstractReport::DEFAULT_API_VERSION 
                  . '/' . AbstractReport::ENDPOINT_REPORT_STATUS;

        $this->report->expects($this->exactly(2))
                     ->method('getEndpoint')
                     ->with($this->equalTo(AbstractReport::ENDPOINT_REPORT_STATUS))
                     ->will($this->returnValue($endpoint));

        $returnPostRequest = ['status' => 'completed'];

        $this->mockedCurl->expects($this->at(0))
                         ->method('postRequest')
                         ->with(
                             $this->equalTo($endpoint),
                             $this->logicalAnd(
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_API_KEY),
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_APP_GUID),
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_TOKEN)
                             ))
                         ->will($this->returnValue($returnPostRequest));


        $job = ['report_token' => 'test_temporary_token'];

        $this->assertContains(
            $returnPostRequest['status'],
            $method->invoke($this->report, $job)
        );

        $returnPostRequest = ['status' => 'error'];

        $this->mockedCurl->expects($this->at(1))
                         ->method('postRequest')
                         ->with(
                             $this->equalTo($endpoint),
                             $this->logicalAnd(
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_API_KEY),
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_APP_GUID),
                                 $this->arrayHasKey(ReportSummaryValues::FIELD_TOKEN)
                             ))
                         ->will($this->returnValue($returnPostRequest));

        $this->expectException(\KochavaReporting\ReportException::class);
        $method->invoke($this->report, $job);
    }

    public function testFetchReport()
    {
        $this->report = $this->getMockedReport(['readReportAsJSON']);
        $pathToReport = 'pathToReport';

        $method = $this->getMethod('fetchReport');

        $returnFromReadReportAsJson = 'json string';

        $this->report->expects($this->once())
                     ->method('readReportAsJSON')
                     ->with($this->equalTo($pathToReport))
                     ->will($this->returnValue($returnFromReadReportAsJson));

        $this->assertEquals(
            $returnFromReadReportAsJson,
            $method->invoke($this->report, $pathToReport)
        );
    }

    public function testReadReportAsJSON()
    {
        if (!is_writable(__DIR__)) {
            throw new Exception('a test directory have to be writeable to perform a test "' . __METHOD__ . '"' . PHP_EOL);
        }

        $report = ['foo' => 'bar'];

        $tmpDir = __DIR__ . DIRECTORY_SEPARATOR . 'tmp';

        mkdir($tmpDir, 0777);

        $pathToReport = $tmpDir . DIRECTORY_SEPARATOR . 'jsonReport.json';

        file_put_contents($pathToReport, json_encode($report));

        $method = $this->getMethod('readReportAsJSON');

        $this->assertArrayHasKey(
            'foo',
            $method->invoke($this->report, $pathToReport)
        );
        unlink($pathToReport);
    }

    public function testGetErrorMsg()
    {
        $method = $this->getMethod('getErrorMsg');

        $params = [
            'error' => 'An error occurred',
        ];

        $this->assertEquals(
            $params['error'],
            $method->invoke($this->report, $params)
        );

        $params = [];

        $this->assertEquals(
            AbstractReport::ERROR_MSG,
            $method->invoke($this->report, $params)
        );
    }

    public function testGetDeliveryFormat()
    {
        $this->assertEquals(
            AbstractReport::DELIVERY_FORMAT_JSON,
            $this->report->getDeliveryFormat()
        );
    }

    protected function getMockedReport(array $methodsToMocking = [])
    {
        return $this->getMockForAbstractClass(
            AbstractReport::class,
            [
                $this->getMockedCurlAdapter(),
                static::TEST_TOKEN,
                static::TEST_ADVERTIZER_ID
            ],
            '',
            true,
            true,
            true,
            $methodsToMocking
        );
    }

    protected function getMockedCurlAdapter()
    {
        if (empty($this->mockedCurl)) {
            $this->mockedCurl = $this->getMockBuilder(CurlAdapter::class)
                        ->setMethods(['postRequest'])
                        ->getMock();
        }

        return $this->mockedCurl;
    }

    protected function getMethod($name)
    {
        $class = new \ReflectionClass($this->report);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}

