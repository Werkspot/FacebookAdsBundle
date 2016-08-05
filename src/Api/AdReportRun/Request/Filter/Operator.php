<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Filter;

use Werkspot\Enum\AbstractEnum;

class Operator extends AbstractEnum
{
    const EQUAL = 'EQUAL';
    const NOT_EQUAL = 'NOT_EQUAL';
    const GREATER_THAN = 'GREATER_THAN';
    const GREATER_THAN_OR_EQUAL = 'GREATER_THAN_OR_EQUAL';
    const LESS_THAN = 'LESS_THAN';
    const LESS_THAN_OR_EQUAL = 'LESS_THAN_OR_EQUAL';
    const IN_RANGE = 'IN_RANGE';
    const NOT_IN_RANGE = 'NOT_IN_RANGE';
    const CONTAIN = 'CONTAIN';
    const NOT_CONTAIN = 'NOT_CONTAIN';
    const IN = 'IN';
    const NOT_IN = 'NOT_IN';
    const STARTS_WITH = 'STARTS_WITH';
    const ANY = 'ANY';
    const ALL = 'ALL';
    const AFTER = 'AFTER';
    const BEFORE = 'BEFORE';
    const NONE = 'NONE';
}
