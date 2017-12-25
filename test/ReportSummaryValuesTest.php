<?php

namespace KochavaReportingTest;

use \KochavaReporting\ReportSummaryValues;

class ReportSummaryValuesTest extends \PHPUnit\Framework\TestCase
{
    public function testTrafficList()
    {
        $trafficList = ReportSummaryValues::getTrafficList();
        $this->assertContains(ReportSummaryValues::TRAFFIC_TYPE_EVENT, $trafficList);
        $this->assertContains(ReportSummaryValues::TRAFFIC_TYPE_CLICK, $trafficList);
        $this->assertContains(ReportSummaryValues::TRAFFIC_TYPE_INSTALL, $trafficList);
        $this->assertContains(ReportSummaryValues::TRAFFIC_TYPE_IMPRESSION, $trafficList);
    }
}

