<?php

namespace KochavaReportingTest;

use \KochavaReporting\ReportDetailValues;

class ReportDetailValuesTest extends \PHPUnit\Framework\TestCase
{
    public function testTrafficList()
    {
        $trafficList = ReportDetailValues::getTrafficList();
        $this->assertContains(ReportDetailValues::TRAFFIC_TYPE_EVENT, $trafficList);
        $this->assertContains(ReportDetailValues::TRAFFIC_TYPE_CLICK, $trafficList);
        $this->assertContains(ReportDetailValues::TRAFFIC_TYPE_INSTALL, $trafficList);
        $this->assertContains(ReportDetailValues::TRAFFIC_TYPE_IMPRESSION, $trafficList);
    }
}

