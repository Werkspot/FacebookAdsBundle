<?php

namespace Werkspot\FacebookAdsBundle\Tests\Model\Insight\Params;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\Insight\Params\TimeRange;

class TimeRangeTest extends PHPUnit_Framework_TestCase
{
    public function testSinceTillDate()
    {
        $since = new \DateTime('-1week');
        $till = new \DateTime('now');

        $result = [
            'since' => $since->format('Y-m-d'),
            'until' => $till->format('Y-m-d')
        ];

        $timeRange = new TimeRange($since, $till);
        $this->assertEquals($result, $timeRange->getParamsArray());
    }

    public function testOneDate()
    {
        $since = new \DateTime('-1month');

        $result = [
            'since' => $since->format('Y-m-d'),
            'until' => $since->format('Y-m-d')
        ];

        $timeRange = new TimeRange($since);
        $this->assertEquals($result, $timeRange->getParamsArray());
    }
}
