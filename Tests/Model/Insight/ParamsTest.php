<?php

namespace Werkspot\FacebookAdsBundle\Tests\Model\Insight;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\ActionReportTime;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\DatePreset;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\Field;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\Level;
use Werkspot\FacebookAdsBundle\Model\Insight\Params;
use Werkspot\FacebookAdsBundle\Model\Insight\Params\TimeRange;

class ParamsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFieldEnumData
     *
     * @params Field $field
     */
    public function testAddField(Field $field)
    {
        $params = new Params();
        $params->addField($field);

        $this->assertEquals(
            ['fields' => $field->getValue()],
            $params->getParamsArray()
        );
    }

    public function testAddAllFields()
    {
        $allField = Field::getValidOptions();
        $params = new Params();
        $params->addAllFields();

        $this->assertEquals(
            ['fields' => implode(', ', $allField)],
            $params->getParamsArray()
        );
    }

    public function testAddFieldsFromString()
    {
        $oneParameter = Field::ACCOUNT_NAME;
        $params = new Params();
        $params->addFieldsFromString($oneParameter);

        $this->assertEquals(
            ['fields' => $oneParameter],
            $params->getParamsArray()
        );

        $twoParameter = Field::ACCOUNT_NAME . ', ' . Field::SPEND;
        $params = new Params();
        $params->addFieldsFromString($twoParameter);

        $this->assertEquals(
            ['fields' => $twoParameter],
            $params->getParamsArray()
        );
    }

    /**
     * @dataProvider getFieldEnumData
     *
     * @params Field $field
     */
    public function testRemoveField(Field $field)
    {
        $params = new Params();
        $params->addField($field);
        $this->assertEquals(
            ['fields' => $field->getValue()],
            $params->getParamsArray()
        );
        $params->removeField($field);

        $this->assertEquals(
            ['fields' => ''],
            $params->getParamsArray()
        );

        $defaultField = Field::get(Field::SPEND);
        if ($field !== $defaultField) {
            $parameters = $field->getValue() . ', ' . $defaultField->getValue();
            $params = new Params();
            $params->addFieldsFromString($parameters);
            $this->assertEquals(
                ['fields' => $parameters],
                $params->getParamsArray()
            );
            $params->removeField($field);

            $this->assertEquals(
                ['fields' => $defaultField->getValue()],
                $params->getParamsArray()
            );
        }
    }

    public function testTimeRange()
    {
        $since = new \DateTime('-12day');
        $timeRange = new TimeRange($since);
        $params = new Params();
        $params->setTimeRange($timeRange);

        $this->assertEquals(['fields' => '', 'time_range' => $timeRange->getParamsArray()], $params->getParamsArray());
    }

    /**
     * @dataProvider getTestDatePresetData
     *
     * @params DatePreset $datePreset
     */
    public function testDatePreset(DatePreset $datePreset)
    {
        $params = new Params();
        $params->setDatePreset($datePreset);

        $this->assertEquals(['fields' => '', 'date_preset' => $datePreset->getValue()], $params->getParamsArray());
    }

    public function getTestDatePresetData()
    {
        $result = [];
        $fields = DatePreset::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [DatePreset::get($field)];
        }

        return $result;
    }

    /**
     * @dataProvider getTestActionReportTimeData
     *
     * @params ActionReportTime $actionReportTime
     */
    public function testActionReportTime(ActionReportTime $actionReportTime)
    {
        $params = new Params();
        $params->setActionReportTime($actionReportTime);

        $this->assertEquals(['fields' => '', 'action_report_time' => $actionReportTime->getValue()], $params->getParamsArray());
    }

    public function getTestActionReportTimeData()
    {
        $result = [];
        $fields = ActionReportTime::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [ActionReportTime::get($field)];
        }

        return $result;
    }

    public function testDefaultSummary()
    {
        $params = new Params();

        $params->setDefaultSummary(true);

        $this->assertEquals(['fields' => '', 'default_summary' => true], $params->getParamsArray());

        $params->setDefaultSummary(false);
        $this->assertEquals(['fields' => '', 'default_summary' => false], $params->getParamsArray());
    }

    /**
     * @dataProvider getTestLevelData
     *
     * @params Level $level
     */
    public function testLevel(Level $level)
    {
        $params = new Params();
        $params->setLevel($level);

        $this->assertEquals(['fields' => '', 'level' => $level->getValue()], $params->getParamsArray());
    }

    public function getTestLevelData()
    {
        $result = [];
        $levels = Level::getValidOptions();
        foreach ($levels as $key => $level) {
            $result[$level] = [Level::get($level)];
        }

        return $result;
    }

    public function getFieldEnumData()
    {
        $result = [];
        $fields = Field::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [Field::get($field)];
        }

        return $result;
    }
}
