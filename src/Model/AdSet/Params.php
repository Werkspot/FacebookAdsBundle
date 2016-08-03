<?php
namespace Werkspot\FacebookAdsBundle\Model\AdSet;

use Werkspot\FacebookAdsBundle\Model\ParamsInterface;
use Werkspot\FacebookAdsBundle\Model\AdSet\Enum\Field;

class Params implements ParamsInterface
{
    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var int
     */
    private $limit;

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getValue()] = $field->getValue();
    }

    /**
     * @param Field $field
     */
    public function removeField(Field $field)
    {
        unset($this->fields[$field->getValue()]);
    }

    /**
     * @param string $fields
     */
    public function addFieldsFromString($fields)
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

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getParamsArray()
    {
        $params = [];

        if ($this->limit) {
            $params['limit'] = (string) $this->limit;
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsArray()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchQuery()
    {
        return '?' . http_build_query($this->getParamsArray());
    }

}
