<?php

namespace Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum;

use Werkspot\Enum\AbstractEnum;

class Level extends AbstractEnum
{
    const AD = 'ad';
    const AD_SET = 'adset';
    const CAMPAIGN = 'campaign';
    const ACCOUNT = 'account';
}
