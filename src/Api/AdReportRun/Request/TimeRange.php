<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun\Request;

use DateTime;

class TimeRange
{
    /** @var DateTime */
    private $since;

    /** @var DateTime */
    private $until;

    public function __construct(DateTime $since, DateTime $until = null)
    {
        $this->since = $since;
        $this->until = $until ?? $since;
    }

    public function toArray(): array
    {
        return [
            'since' => $this->since->format('YYYY-MM-DD'),
            'until' => $this->until->format('YYYY-MM-DD'),
        ];
    }

}
