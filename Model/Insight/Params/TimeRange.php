<?php

namespace Werkspot\FacebookAdsBundle\Model\Insight\Params;

use DateTime;

class TimeRange
{
    /**
     * @var DateTime
     */
    private $since;

    /**
     * @var DateTime
     */
    private $till;

    /**
     * @param DateTime $since
     * @param DateTime|null $till
     */
    public function __construct(DateTime $since, DateTime $till = null)
    {
        $this->since = $since;
        $this->till = ($till) ? $till : $since;
    }

    public function getParamsArray()
    {
        return [
            'since' => $this->since->format('Y-m-d'),
            'until' => $this->till->format('Y-m-d')
        ];
    }
}
