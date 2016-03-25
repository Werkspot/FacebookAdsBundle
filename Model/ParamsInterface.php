<?php
namespace Werkspot\FacebookAdsBundle\Model;

interface ParamsInterface
{
    /**
     * @return array
     */
    public function getParamsArray();

    /**
     * @return string
     */
    public function getBatchQuery();
}
