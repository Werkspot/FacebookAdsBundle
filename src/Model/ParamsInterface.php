<?php
namespace Werkspot\FacebookAdsBundle\Model;

interface ParamsInterface
{
    /**
     * @return array
     */
    public function getParamsArray();

    /**
     * @return array
     */
    public function getFieldsArray();

    /**
     * @return string
     */
    public function getBatchQuery();
}
