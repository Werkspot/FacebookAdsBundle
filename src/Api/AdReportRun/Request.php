<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun;

use Facebook\FacebookRequest;
use Werkspot\FacebookAdsBundle\Api\AdReportRun;
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
use Werkspot\FacebookAdsBundle\Api\ClientInterface;

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

    /** @var SummaryActionBreakdown[] */
    private $summaryActionBreakdowns;

    /** @var TimeIncrement */
    private $timeIncrement;

    /** @var int */
    private $timeIncrementDay;

    /** @var TimeRange[] */
    private $timeRanges;

    public function __construct(string $accountId, ClientInterface $client)
    {
        $this->accountId = $accountId;
        $this->client = $client;
        $this->actionAttributionWindows = ActionAttributionWindows::get(ActionAttributionWindows::DEFAULT);
        $this->actionBreakdowns[ActionBreakdown::TYPE] = ActionBreakdown::get(ActionBreakdown::TYPE);
        $this->actionReportTime = ActionReportTime::get(ActionReportTime::IMPRESSION);
        $this->breakdowns = [];
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

    public function addActionBreakdown(ActionBreakdown $actionBreakdown)
    {
        $this->actionBreakdowns[$actionBreakdown->getValue()] = $actionBreakdown;
    }

    public function removeActionBreakdown(ActionBreakdown $actionBreakdown)
    {
        unset($this->actionBreakdowns[$actionBreakdown->getValue()]);
    }

    public function setActionReportTime(ActionReportTime $actionReportTime)
    {
        $this->actionReportTime = $actionReportTime;
    }

    public function setBreakdown(Breakdown $breakdown)
    {
        $this->breakdowns = [];
        $this->addBreakdown($breakdown);
    }

    public function addBreakdown(Breakdown $breakdown)
    {
        $this->validateBreakdown($breakdown);
        $this->breakdowns[$breakdown->getValue()] = $breakdown;
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
            Breakdown::get(Breakdown::IMPRESSION_DEVICE),
            Breakdown::get(Breakdown::PLACEMENT),
            Breakdown::get(Breakdown::AGE),
            Breakdown::get(Breakdown::GENDER),
        ];
        if (count($this->breakdowns) > 0) {
            if (in_array($breakdown, $allowCombination) === false) {
                throw new BreakdownValueNotAllowedException('Can\'t combine breakdown. allowed combinations are: ["age", "gender"] and ["impression_device", "placement"].');
            } elseif (in_array(reset($this->breakdowns), $allowCombination) === false) {
                throw new BreakdownValueNotAllowedException('More than one breakdown is not supported, except ["age", "gender"] and ["impression_device", "placement"].');
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

    public function addExportColumn(Field $field)
    {
        $this->exportColumns[$field->getValue()] = $field->getValue();
    }

    public function removeExportColumn(Field $field)
    {
        unset($this->exportColumns[$field->getValue()]);
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

    public function addSummaryActionBreakdown(SummaryActionBreakdown $summaryActionBreakdown)
    {
        $this->summaryActionBreakdowns[$summaryActionBreakdown->getValue()] = $summaryActionBreakdown;
    }

    public function removeSummaryActionBreakdown(SummaryActionBreakdown $summaryActionBreakdown)
    {
        unset($this->summaryActionBreakdowns[$summaryActionBreakdown->getValue()]);
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

    /**
     * @return ActionAttributionWindows
     */
    public function getActionAttributionWindows(): ActionAttributionWindows
    {
        return $this->actionAttributionWindows;
    }

    /**
     * @return Enum\ActionBreakdown[]
     */
    public function getActionBreakdowns(): array
    {
        return $this->actionBreakdowns;
    }

    /**
     * @return ActionReportTime
     */
    public function getActionReportTime(): ActionReportTime
    {
        return $this->actionReportTime;
    }

    /**
     * @return Enum\Breakdown[]
     */
    public function getBreakdowns(): array
    {
        return $this->breakdowns;
    }

    /**
     * @return DatePreset
     */
    public function getDatePreset(): DatePreset
    {
        return $this->datePreset;
    }

    /**
     * @return boolean
     */
    public function isDefaultSummary(): bool
    {
        return $this->defaultSummary;
    }

    /**
     * @return Enum\Field[]
     */
    public function getExportColumns(): array
    {
        return $this->exportColumns;
    }

    /**
     * @return ExportFormat
     */
    public function getExportFormat(): ExportFormat
    {
        return $this->exportFormat;
    }

    /**
     * @return string
     */
    public function getExportName(): string
    {
        return $this->exportName;
    }

    /**
     * @return Enum\Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return Request\Filter[]
     */
    public function getFiltering(): array
    {
        return $this->filtering;
    }

    /**
     * @return Level
     */
    public function getLevel(): Level
    {
        return $this->level;
    }

    /**
     * @return int
     */
    public function getProductIdLimit(): int
    {
        return $this->productIdLimit;
    }

    /**
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @return Enum\Field[]
     */
    public function getSummary(): array
    {
        return $this->summary;
    }

    /**
     * @return SummaryActionBreakdown[]
     */
    public function getSummaryActionBreakdowns(): array
    {
        return $this->summaryActionBreakdowns;
    }

    /**
     * @return TimeIncrement
     */
    public function getTimeIncrement(): TimeIncrement
    {
        return $this->timeIncrement;
    }

    /**
     * @return int
     */
    public function getTimeIncrementDay(): int
    {
        return $this->timeIncrementDay;
    }

    /**
     * @return Request\TimeRange[]
     */
    public function getTimeRanges(): array
    {
        return $this->timeRanges;
    }

    public function getAdReportRun(): AdReportRun
    {
        $request = new FacebookRequest(
            $this->client->getFacebookApp(),
            $this->client->getAccessToken(),
            'GET',
            "/act_{$this->accountId}/insights",
            $this->getPostData()
        );
        $response = $this->client->getFacebookClient()->sendRequest($request);
        return new AdReportRun($response->getDecodedBody()['report_run_id'], $this->client);

    }

    private function getPostData(): array
    {
        $postData = [];
        $postData['date_preset'] = 'yesterday';
        $postData['level'] = 'adset';
        $postData['fields'] = "['adset_name' ,'adset_id', 'account_id', 'account_name', 'spend', 'clicks', 'impressions', 'reach', 'unique_clicks', 'unique_impressions']";
        $postData['filtering'] = '[{"field":"adset.spent","operator":"GREATER_THAN","value":0}]';

        return $postData;
    }
}
