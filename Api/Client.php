<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Facebook\Facebook;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use Werkspot\FacebookAdsBundle\Api\Exception\InsightsTimeoutException;
use Werkspot\FacebookAdsBundle\Model\AdSet\Params as AdSetParams;
use Werkspot\FacebookAdsBundle\Model\Batch;
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
     * @param int $accountId
     * @param null|AdSetParams $params
     *
     * @return \FacebookAds\Cursor
     */
    public function getAdSetFromAccount($accountId, AdSetParams $params = null)
    {
        $this->initApi(); //-- important! always init the API

        $account = new AdAccount('act_'.$accountId);
        $params = ($params) ? $params->getParamsArray() : [];
        $adSets = $account->getAdSets($params);

        return $adSets;
    }

    public function getFromBulk(Batch $batch)
    {
        $facebook = $this->initFacebook();
        $response = $facebook->post('/', $batch->getArray());

        return $response->getBody();
    }

}
