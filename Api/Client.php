<?php

namespace Werkspot\FacebookAdsBundle\Api;

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use Werkspot\FacebookAdsBundle\Api\Exception\InsightsTimeoutException;
use Werkspot\FacebookAdsBundle\Model\Insight\Params as InsightParams;

class Client extends AbstractClient
{
    /**
     * @param int $campaignId
     * @param InsightParams|null $params
     *
     * @throws InsightsTimeoutException
     *
     * @return \FacebookAds\Cursor
     */
    public function getInsights($campaignId, InsightParams $params = null)
    {
        $this->initApi(); //-- important! always init the API
        $campaign = new Campaign($campaignId);
        $params = ($params) ? $params->getParamsArray() : [];
        $asyncJob = $campaign->getInsightsAsync([], $params);

        $timeout = 0;
        while (!$asyncJob->isComplete()) {
            $asyncJob->read();
            ++$timeout;
            sleep(1);

            if ($timeout > 300) {
                throw new InsightsTimeoutException();
            }
        }

        return $asyncJob->getResult();
    }

    /**
     * @param AdAccount $account
     * @param array $params
     *
     * @return \FacebookAds\Cursor
     */
    public function getAdSetFromAccount(AdAccount $account, array $params = [])
    {
        $this->initApi(); //-- important! always init the API

        $adSets = $account->getAdSets($params);

        return $adSets;
    }
}
