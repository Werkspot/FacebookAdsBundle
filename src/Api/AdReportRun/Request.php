<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun;

use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionAttributionWindows;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionBreakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionReportTime;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Breakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\DatePreset;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ExportFormat;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Field;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Level;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\SummaryActionBreakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\TimeIncrement;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\BreakdownValueNotAllowedException;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\TimeIncrementDayValueNotValidException;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\TimeIncrementValueNotValidException;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Filter;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\TimeRange;
use Werkspot\FacebookAdsBundle\Api\Client;

class Request
{
    /** @var string */
    private $accountId;

    /** @var Client */
    private $client;

    /** @var ActionAttributionWindows */
    private $actionAttributionWindows;

    /** @var ActionBreakdown[] */
    private $actionBreakdowns;

    /** @var ActionReportTime */
    private $actionReportTime;

    /** @var Breakdown[] */
    private $breakdowns;

    /** @var DatePreset */
    private $datePreset;

    /** @var bool */
    private $defaultSummary;

    /** @var Field[] */
    private $exportColumns;

    /** @var ExportFormat */
    private $exportFormat;

    /** @var string */
    private $exportName;

    /** @var Field[] */
    private $fields;

    /** @var Filter[] */
    private $filtering;

    /** @var Level */
    private $level;

    /** @var int */
    private $productIdLimit;

    /** @var array */
    private $sort;

    /** @var Field[] */
    private $summary;

    /** @var SummaryActionBreakdown */
    private $summaryActionBreakdowns;

    /** @var TimeIncrement */
    private $timeIncrement;

    /** @var int */
    private $timeIncrementDay;

    /** @var TimeRange[] */
    private $timeRanges;

    public function __construct(string $accountId, Client $client)
    {
        $this->accountId = $accountId;
        $this->client = $client;
        $this->actionAttributionWindows = ActionAttributionWindows::get(ActionAttributionWindows::DEFAULT);
        $this->actionBreakdowns[ActionBreakdown::TYPE] = ActionBreakdown::get(ActionBreakdown::TYPE);
        $this->actionReportTime = ActionReportTime::get(ActionReportTime::IMPRESSION);
        $this->datePreset =  DatePreset::get(DatePreset::LAST_30_DAYS);
        $this->defaultSummary = false;
        $this->exportColumns = [];
        $this->summaryActionBreakdowns[SummaryActionBreakdown::ACTION_TYPE] = SummaryActionBreakdown::get(SummaryActionBreakdown::ACTION_TYPE);
        $this->timeIncrement =  TimeIncrement::get(TimeIncrement::ALL_DAYS);

    }

    public function setActionAttributionWindows(ActionAttributionWindows $actionAttributionWindows)
    {
        $this->actionAttributionWindows = $actionAttributionWindows;
    }

    public function setActionBreakdowns(ActionBreakdown $actionBreakdowns)
    {
        $this->actionBreakdowns = $actionBreakdowns;
    }

    public function setActionReportTime(ActionReportTime $actionReportTime)
    {
        $this->actionReportTime = $actionReportTime;
    }

    public function setBreakdowns(Breakdown $breakdown)
    {
        $this->breakdowns = [];
        $this->addBreakdowns($breakdown);
    }

    public function addBreakdowns(Breakdown $breakdown)
    {
        $this->validateBreakdown($breakdown);
        $this->breakdowns[] = $breakdown;
    }


    /**
     * @param Breakdown $breakdown
     * @throws BreakdownValueNotAllowedException
     */
    private function validateBreakdown(Breakdown $breakdown)
    {
        $this->validateMultipleBreakdowns($breakdown);
        $this->validateBreakdownCombination($breakdown);
    }

    private function validateMultipleBreakdowns(Breakdown $breakdown)
    {
        $allowCombination = [
            Breakdown::IMPRESSION_DEVICE,
            Breakdown::PLACEMENT,
            Breakdown::AGE,
            Breakdown::GENDER,
        ];

        if (count($this->breakdowns) > 0) {
            if (array_search($allowCombination, $this->breakdowns) == false) {
                throw new BreakdownValueNotAllowedException('More than one breakdown is not supported, except ["age", "gender"] and ["impression_device", "placement"].');
            } elseif (in_array($breakdown, $allowCombination) == false) {
                throw new BreakdownValueNotAllowedException('Can\'t combine breakdown. allowed combinations are: ["age", "gender"] and ["impression_device", "placement"].');
            }
        }
    }

    private function validateBreakdownCombination(Breakdown $breakdown)
    {
        if (
            $breakdown->getValue() == Breakdown::IMPRESSION_DEVICE &&
            !array_key_exists(Breakdown::PLACEMENT, $this->breakdowns)
        ) {
            throw new BreakdownValueNotAllowedException('The option `IMPRESSION_DEVICE` is only valid in combination with `PLACEMENT` please add it first.');
        }
    }

    public function setDatePreset(DatePreset $datePreset)
    {
        $this->datePreset = $datePreset;
    }

    public function setDefaultSummary(bool $defaultSummary)
    {
        $this->defaultSummary = $defaultSummary;
    }

    public function setExportColumns(array $exportColumns)
    {
        $this->exportColumns = $exportColumns;
    }

    public function setExportFormat(ExportFormat $exportFormat)
    {
        $this->exportFormat = $exportFormat;
    }

    public function setExportName(string $exportName)
    {
        $this->exportName = $exportName;
    }

    public function addField(Field $field)
    {
        $this->fields[$field->getValue()] = $field->getValue();
    }

    public function removeField(Field $field)
    {
        unset($this->fields[$field->getValue()]);
    }

    public function addFieldsFromString(string $fields)
    {
        $fields = explode(',', preg_replace('/\s+/', '', $fields));
        $availableFields = Field::getValidOptions();
        foreach ($fields as $field) {
            if (in_array($field, $availableFields)) {
                $this->fields[$field] = $field;
            }
        }
    }

    public function addAllFields()
    {
        $availableFields = Field::getValidOptions();
        foreach ($availableFields as $field) {
            $this->fields[$field] = $field;
        }
    }

    public function addFilter(Filter $filter)
    {
        $this->filtering[$filter->getId()] = $filter;
    }

    public function removeFilter(Filter $filter)
    {
        unset($this->filtering[$filter->getId()]);
    }

    public function setLevel(Level $level)
    {
        $this->level = $level;
    }

    public function setProductIdLimit(int $productIdLimit)
    {
        $this->productIdLimit = $productIdLimit;
    }

    public function setSort(array $sort)
    {
        $this->sort = $sort;
    }
    public function setSummary(array $summary)
    {
        $this->summary = $summary;
    }

    public function setSummaryActionBreakdowns(SummaryActionBreakdown $summaryActionBreakdowns)
    {
        $this->summaryActionBreakdowns = $summaryActionBreakdowns;
    }

    public function setTimeIncrement(TimeIncrement $timeIncrement)
    {
        if ($timeIncrement->getValue() == TimeIncrement::INTEGER && !$this->timeIncrementDay) {
            throw new TimeIncrementValueNotValidException('You can\'t use value \'integer\' if `timeIncrementDays` is NULL');
        }

        $this->timeIncrement = $timeIncrement;
        $this->timeIncrementDay = null;
    }

    public function setTimeIncrementDay(int $timeIncrementDay)
    {
        if ($timeIncrementDay < 1 || $timeIncrementDay > 90) {
            throw new TimeIncrementDayValueNotValidException('Only number of days 1 to 90 is supported');
        }

        $this->timeIncrementDay = $timeIncrementDay;
        $this->timeIncrement = TimeIncrement::get(TimeIncrement::INTEGER);
    }

    public function addTimeRanges(TimeRange $timeRange)
    {
        $this->timeRanges[] = $timeRange;
    }
}
