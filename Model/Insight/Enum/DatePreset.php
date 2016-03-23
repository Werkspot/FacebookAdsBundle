<?php

namespace Werkspot\FacebookAdsBundle\Model\Insight\Enum;

use Werkspot\ApiLibrary\Enum\AbstractEnum;

class DatePreset extends AbstractEnum
{
    const TODAY = 'today';
    const YESTERDAY = 'yesterday';
    const LAST_3_DAYS = 'last_3_days';
    const THIS_WEEK = 'this_week';
    const LAST_WEEK = 'last_week';
    const LAST_7_DAYS = 'last_7_days';
    const LAST_14_DAYS = 'last_14_days';
    const LAST_28_DAYS = 'last_28_days';
    const LAST_30_DAYS = 'last_30_days';
    const LAST_90_DAYS = 'last_90_days';
    const THIS_MONTH = 'this_month';
    const LAST_MONTH = 'last_month';
    const THIS_QUARTER = 'this_quarter';
    const LAST_3_MONTHS = 'last_3_months';
    const LIFETIME = 'lifetime';
}
