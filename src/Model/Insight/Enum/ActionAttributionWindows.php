<?php
namespace Werkspot\FacebookAdsBundle\Model\Insight\Enum;

use Werkspot\Enum\AbstractEnum;

class ActionAttributionWindows extends AbstractEnum
{
    const ONE_DAY_VIEW = '1d_view';
    const WEEK_VIEW = '7d_view';
    const FOUR_WEEK_VIEW = '28d_view';
    const ONE_DAY_CLICK = '1d_click';
    const WEEK_CLICK = '7d_click';
    const FOUR_WEEK_CLICK = '28d_click';
    const DEFAULT = 'default';
}
