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
        $cursor = $account->getAdSets($params->getFieldsArray(), $params->getParamsArray());

        return $cursor;
    }

    public function getFromBatch(Batch $batch)
    {
        $responseBody = [];
        $facebook = $this->initFacebook();
        $batchArray = array_chunk($batch->getArray(), Batch::MAX_REQUESTS_PER_BATCH);
        foreach ($batchArray as $batch) {
            
            $response = $facebook->post('/', ['batch' => json_encode($batch)]);
            $body = json_decode($response->getBody());
            $responseBody = array_merge($responseBody, $body);
            
            // To prevent from api access limits
            sleep(120);
        }

        return $responseBody;
    }

}
