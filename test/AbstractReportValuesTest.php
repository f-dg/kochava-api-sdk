<?php

namespace KochavaReportingTest;

use \KochavaReporting\AbstractReportValues;

class AbstractReportValuesTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTrafficList()
    {
        $shouldBeEmptyArray = AbstractReportValues::getTrafficList();
        $this->assertTrue(empty($shouldBeEmptyArray));
        $this->assertTrue(is_array($shouldBeEmptyArray));
    }
}

