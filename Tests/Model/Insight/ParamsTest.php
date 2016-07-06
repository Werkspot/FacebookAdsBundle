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
    public function testNewParamsIsEmpty()
    {
        $params = new Params();
        $this->assertEquals('?', $params->getBatchQuery());
        $this->assertEquals([], $params->getParamsArray());
        $this->assertEquals([], $params->getFieldsArray());
    }

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
            [$field->getValue() => $field->getValue()],
            $params->getFieldsArray()
        );
        $this->assertEquals(['fields' => $field], $params->getParamsArray());
    }

    public function testAddAllFields()
    {
        $allField = Field::getValidOptions();
        $params = new Params();

        $params->addAllFields();
        $this->assertEquals(['fields' => implode(',', $allField)], $params->getParamsArray());
        foreach ($allField as $field){
            $this->assertTrue(in_array($field, $params->getFieldsArray()));
        }
    }

    public function testAddFieldsFromString()
    {
        $oneParameter = Field::ACCOUNT_NAME;
        $params = new Params();

        $params->addFieldsFromString($oneParameter);
        $this->assertEquals(
            [$oneParameter => $oneParameter],
            $params->getFieldsArray()
        );

        $twoParameter = [Field::ACCOUNT_NAME, Field::SPEND];
        $params = new Params();

        $params->addFieldsFromString(implode(', ', $twoParameter));
        foreach ($twoParameter as $field){
            $this->assertTrue(in_array($field, $params->getFieldsArray()));
        }
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
            [$field->getValue() => $field->getValue()],
            $params->getFieldsArray()
        );
        $params->removeField($field);

        $this->assertEquals(
            [],
            $params->getFieldsArray()
        );
    }

    /**
     * @dataProvider getFieldEnumData
     *
     * @params Field $field
     */
    public function testRemoveFieldWithAdditionalField(Field $field)
    {
        $defaultField = Field::get(Field::SPEND);
        if ($field !== $defaultField) {
            $parameters = [$field->getValue(), $defaultField->getValue()];
            $params = new Params();
            $params->addFieldsFromString(implode(', ', $parameters));
            foreach ($parameters as $testField){
                $this->assertTrue(in_array($testField, $params->getFieldsArray()));
            }

            $params->removeField($field);

            $this->assertEquals(
                [$defaultField->getValue() => $defaultField->getValue()],
                $params->getFieldsArray()
            );
        }
    }

    public function testTimeRange()
    {
        $since = new \DateTime('-12day');
        $timeRange = new TimeRange($since);
        $params = new Params();
        $this->assertEquals([], $params->getParamsArray());

        $params->setTimeRange($timeRange);
        $this->assertEquals(['time_range' => $timeRange->getParamsArray()], $params->getParamsArray());
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
        $this->assertEquals(['date_preset' => $datePreset->getValue()], $params->getParamsArray());
        $this->assertEquals('?date_preset=' . $datePreset->getValue(), $params->getBatchQuery());
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
        $this->assertEquals(['action_report_time' => $actionReportTime->getValue()], $params->getParamsArray());
        $this->assertEquals('?action_report_time=' . $actionReportTime->getValue(), $params->getBatchQuery());
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
        $this->assertEquals(['default_summary' => true], $params->getParamsArray());
        $this->assertEquals('?default_summary=1', $params->getBatchQuery());

        $params->setDefaultSummary(false);
        $this->assertEquals(['default_summary' => false], $params->getParamsArray());
        $this->assertEquals('?default_summary=0', $params->getBatchQuery());
    }

    public function testFiltering()
    {
        $filter = 'WHERE COST > 0';
        $params = new Params();

        $params->setFiltering($filter);
        $this->assertEquals(['filtering' => $filter], $params->getParamsArray());
        $this->assertEquals('?filtering='.urlencode($filter), $params->getBatchQuery());
        $this->assertEquals($filter, $params->getFiltering());

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
        $this->assertEquals(['level' => $level->getValue()], $params->getParamsArray());
        $this->assertEquals('?level=' . $level->getValue(), $params->getBatchQuery());
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
