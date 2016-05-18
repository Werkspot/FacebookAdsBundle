<?php
namespace Werkspot\FacebookAdsBundle\Tests\Model\AdSet\Params;

use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Model\AdSet\Enum\Field;
use Werkspot\FacebookAdsBundle\Model\AdSet\Params;

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
     * @dataProvider fieldEnumData
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
    }

    public function testAddAllFields()
    {
        $allField = Field::getValidOptions();
        $params = new Params();

        $params->addAllFields();
        foreach ($allField as $field){
            $this->assertTrue(in_array($field, $params->getFieldsArray()));
        }
    }

    public function testAddFieldsFromString()
    {
        $oneParameter = Field::BUDGET_REMAINING;
        $params = new Params();

        $params->addFieldsFromString($oneParameter);
        $this->assertEquals(
            [$oneParameter => $oneParameter],
            $params->getFieldsArray()
        );

        $twoParameter = [Field::PROMOTED_OBJECT, Field::AD_SET_SCHEDULE];
        $params = new Params();

        $params->addFieldsFromString(implode(', ', $twoParameter));
        foreach ($twoParameter as $field){
            $this->assertTrue(in_array($field, $params->getFieldsArray()));
        }
    }

    /**
     * @dataProvider fieldEnumData
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
     * @dataProvider fieldEnumData
     *
     * @params Field $field
     */
    public function testRemoveFieldWithAdditionalField(Field $field)
    {
        $defaultField = Field::get(Field::ACCOUNT_ID);
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
    
    public function testLimit()
    {
        $limit = 11;
        $params = new Params();

        $params->setLimit($limit);
        $this->assertEquals(['limit' => $limit], $params->getParamsArray());
        $this->assertEquals('?limit=' . $limit, $params->getBatchQuery());
    }

    /**
     * @return array
     */
    public function fieldEnumData()
    {
        $result = [];
        $fields = Field::getValidOptions();
        foreach ($fields as $key => $field) {
            $result[$field] = [Field::get($field)];
        }

        return $result;
    }
}
