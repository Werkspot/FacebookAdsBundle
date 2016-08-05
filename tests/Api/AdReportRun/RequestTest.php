<?php declare(strict_types=1);
namespace WerkspotTest\FacebookAdsBundle\Api\AdReportRun;

use PHPUnit_Framework_TestCase;
use Werkspot\Enum\AbstractEnum;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionAttributionWindows;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionBreakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\ActionReportTime;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Breakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\DatePreset;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Field;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\Level;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\SummaryActionBreakdown;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Enum\TimeIncrement;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\BreakdownValueNotAllowedException;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Filter;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Filter\Operator;
use WerkspotFake\FacebookAdsBundle\Api\FakeClient;

class RequestTest extends PHPUnit_Framework_TestCase
{
    const ACCOUNT_ID = '1234567890';

    public function testDefaults()
    {
        $actionAttributionWindows = ActionAttributionWindows::get(ActionAttributionWindows::DEFAULT);
        $actionBreakdowns = [ActionBreakdown::TYPE => ActionBreakdown::get(ActionBreakdown::TYPE)];
        $actionReportTime = ActionReportTime::get(ActionReportTime::IMPRESSION);
        $datePreset =  DatePreset::get(DatePreset::LAST_30_DAYS);
        $defaultSummary = false;
        $exportColumns = [];
        $summaryActionBreakdowns = [SummaryActionBreakdown::ACTION_TYPE => SummaryActionBreakdown::get(SummaryActionBreakdown::ACTION_TYPE)];
        $timeIncrement =  TimeIncrement::get(TimeIncrement::ALL_DAYS);

        $request = $this->getRequest();

        $this->assertEquals($actionAttributionWindows, $request->getActionAttributionWindows());
        $this->assertEquals($actionBreakdowns, $request->getActionBreakdowns());
        $this->assertEquals($actionReportTime, $request->getActionReportTime());
        $this->assertEquals($datePreset, $request->getDatePreset());
        $this->assertEquals($defaultSummary, $request->isDefaultSummary());
        $this->assertEquals($exportColumns, $request->getExportColumns());
        $this->assertEquals($summaryActionBreakdowns, $request->getSummaryActionBreakdowns());
        $this->assertEquals($timeIncrement, $request->getTimeIncrement());
    }

    /** @dataProvider getActionAttributionWindows */
    public function testSetActionAttributionWindows(ActionAttributionWindows $actionAttributionWindows)
    {
        $request = $this->getRequest();
        $request->setActionAttributionWindows($actionAttributionWindows);
        $this->assertEquals($actionAttributionWindows, $request->getActionAttributionWindows());
    }

    /** @return ActionAttributionWindows[] */
    public function getActionAttributionWindows()
    {
        return $this->getEnumValues(ActionAttributionWindows::class);
    }

    /** @dataProvider getActionBreakdowns */
    public function testAddRemoveActionBreakdowns(ActionBreakdown $actionBreakdown)
    {
        $expectedResult = [ActionBreakdown::TYPE  => ActionBreakdown::get(ActionBreakdown::TYPE)];
        $expectedResult[$actionBreakdown->getValue()] = $actionBreakdown;

        $request = $this->getRequest();
        $request->addActionBreakdown($actionBreakdown);
        $this->assertEquals($expectedResult, $request->getActionBreakdowns());

        $request->removeActionBreakdown($actionBreakdown);
        unset($expectedResult[$actionBreakdown->getValue()]);
        $this->assertEquals($expectedResult, $request->getActionBreakdowns());
    }

    /** @return ActionBreakdown[] */
    public function getActionBreakdowns()
    {
        return $this->getEnumValues(ActionBreakdown::class);
    }

    /** @dataProvider getActionReportTime */
    public function testSetActionReportTime(ActionReportTime $actionReportTime)
    {
        $request = $this->getRequest();
        $request->setActionReportTime($actionReportTime);
        $this->assertEquals($actionReportTime, $request->getActionReportTime());
    }

    /** @return ActionReportTime[] */
    public function getActionReportTime()
    {
        return $this->getEnumValues(ActionReportTime::class);
    }

    public function testAddBreakdown_Age_Gender()
    {
        $expected = [
          Breakdown::AGE => Breakdown::get(Breakdown::AGE),
          Breakdown::GENDER => Breakdown::get(Breakdown::GENDER),
        ];
        $request = $this->getRequest();
        $request->addBreakdown(Breakdown::get(Breakdown::AGE));
        $request->addBreakdown(Breakdown::get(Breakdown::GENDER));
        $this->assertEquals($expected, $request->getBreakdowns());
    }

    public function testAddBreakdown_PLACEMENT_IMPRESSION_DEVICE()
    {
        $expected = [
            Breakdown::PLACEMENT => Breakdown::get(Breakdown::PLACEMENT),
            Breakdown::IMPRESSION_DEVICE => Breakdown::get(Breakdown::IMPRESSION_DEVICE),
        ];
        $request = $this->getRequest();
        $request->addBreakdown(Breakdown::get(Breakdown::PLACEMENT));
        $request->addBreakdown(Breakdown::get(Breakdown::IMPRESSION_DEVICE));
        $this->assertEquals($expected, $request->getBreakdowns());
    }

    /** @expectedException \Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\BreakdownValueNotAllowedException */
    public function testAddBreakdown_Age_Country_shouldThrowBreakdownValueNotAllowedException()
    {
        $request = $this->getRequest();
        $request->addBreakdown(Breakdown::get(Breakdown::AGE));
        $request->addBreakdown(Breakdown::get(Breakdown::COUNTRY));
    }

    /** @expectedException \Werkspot\FacebookAdsBundle\Api\AdReportRun\Request\Exception\BreakdownValueNotAllowedException */
    public function testAddBreakdown_CountryRegion_shouldThrowBreakdownValueNotAllowedException()
    {
        $request = $this->getRequest();
        $request->addBreakdown(Breakdown::get(Breakdown::COUNTRY));
        $request->addBreakdown(Breakdown::get(Breakdown::REGION));
    }

    /** @dataProvider getBreakdown */
    public function testSetBreakdown(Breakdown $breakdown)
    {
        $request = $this->getRequest();
        //-- Breakdown::IMPRESSION_DEVICE can only be added in combination with Breakdown::PLACEMENT which need to be added FIRST
        if ($breakdown->getValue() == Breakdown::IMPRESSION_DEVICE) {
            $this->expectException(BreakdownValueNotAllowedException::class);
        }
        $request->setBreakdown($breakdown);
        $this->assertEquals([$breakdown->getValue() => $breakdown], $request->getBreakdowns());
    }

    /** @return Breakdown[] */
    public function getBreakdown()
    {
        return $this->getEnumValues(Breakdown::class);
    }

    /** @dataProvider getDatePreset */
    public function testSetDatePreset(DatePreset $datePreset)
    {
        $request = $this->getRequest();
        $request->setDatePreset($datePreset);
        $this->assertEquals($datePreset, $request->getDatePreset());
    }

    /** @return DatePreset[] */
    public function getDatePreset()
    {
        return $this->getEnumValues(DatePreset::class);
    }

    /** @dataProvider getField */
    public function testAddField(Field $field)
    {
        $request = $this->getRequest();
        $request->addField($field);
        $this->assertEquals([$field->getValue() => $field->getValue()], $request->getFields());
    }

    /** @dataProvider getField */
    public function testRemoveField(Field $secondField)
    {
        $firstField = Field::get(Field::ACCOUNT_ID);
        if ($firstField !== $secondField) {
            $request = $this->getRequest();
            $request->addField($firstField);
            $request->addField($secondField);
            $expected = [$firstField->getValue() => $firstField->getValue()];
            $expected[$secondField->getValue()] = $secondField->getValue();
            $this->assertEquals($expected, $request->getFields());
            $request->removeField($secondField);
            $this->assertEquals([$firstField->getValue() => $firstField->getValue()], $request->getFields());
        }
    }

    /** @dataProvider getField */
    public function testAddFieldsFromString(Field $field)
    {
        $request = $this->getRequest();
        $request->addFieldsFromString($field->getValue());
        $this->assertEquals([$field->getValue() => $field->getValue()], $request->getFields());
    }

    /** @dataProvider getField */
    public function testAddMultipleFieldsFromString(Field $secondField)
    {
        $firstField = Field::get(Field::ACCOUNT_ID);
        if ($firstField !== $secondField) {
            $request = $this->getRequest();
            $request->addFieldsFromString("{$firstField->getValue()}, {$secondField->getValue()}");
            $this->assertEquals(
                [
                    $firstField->getValue() => $firstField->getValue(),
                    $secondField->getValue() => $secondField->getValue()
                ],
                $request->getFields());
        }
    }

    /** @return Field[] */
    public function getField()
    {
        return $this->getEnumValues(Field::class);
    }

    public function testAddRemoveFilter()
    {
        $filter =  new Filter(Field::get(Field::SPEND), Operator::get(Operator::GREATER_THAN), '0');

        $request = $this->getRequest();
        $request->addFilter($filter);
        $this->assertEquals([$filter->getId() => $filter], $request->getFiltering());

        $request->removeFilter($filter);
        $this->assertEquals([], $request->getFiltering());
    }

    /** @dataProvider getLevel */
    public function testSetLevel(Level $level)
    {
        $request = $this->getRequest();
        $request->setLevel($level);
        $this->assertEquals($level, $request->getLevel());
    }

    /** @return Level[] */
    public function getLevel()
    {
        return $this->getEnumValues(Level::class);
    }

    /** @dataProvider getSummaryActionBreakdown */
    public function testSetSummaryActionBreakdown(SummaryActionBreakdown $summaryActionBreakdown)
    {
        $default = SummaryActionBreakdown::get(SummaryActionBreakdown::ACTION_TYPE);
        $request = $this->getRequest();
        $request->addSummaryActionBreakdown($summaryActionBreakdown);
        $this->assertEquals(
            [
                $default->getValue() => $default,
                $summaryActionBreakdown->getValue() => $summaryActionBreakdown,
            ],
            $request->getSummaryActionBreakdowns()
        );
        $request->removeSummaryActionBreakdown($summaryActionBreakdown);

        $expected = ($summaryActionBreakdown !== $default) ? [$default->getValue() => $default] : [];
        $this->assertEquals($expected, $request->getSummaryActionBreakdowns());
    }

    /** @return SummaryActionBreakdown[] */
    public function getSummaryActionBreakdown()
    {
        return $this->getEnumValues(SummaryActionBreakdown::class);
    }

    /**
     * @param string $enumClass
     * @return AbstractEnum[]
     */
    private function getEnumValues(string $enumClass)
    {
        $result = [];
        /** @var AbstractEnum $enumClass */
        $enums = $enumClass::getValidOptions();
        foreach ($enums as $key => $enum) {
            $result[$enum] = [$enumClass::get($enum)];
        }
        return $result;
    }

    private function getRequest()
    {
        return new Request(self::ACCOUNT_ID, new FakeClient());
    }
}
