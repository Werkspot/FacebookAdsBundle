<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun\Request;

use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Field;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Filter\Operator;

class Filter
{
    /** @var Field */
    private $field;

    /** @var Operator */
    private $operator;

    /** @var string */
    private $value;

    public function __construct(Field $field, Operator $operator, string $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getId(): string
    {
        return md5($this->field->getValue().$this->operator->getValue());
    }

}
