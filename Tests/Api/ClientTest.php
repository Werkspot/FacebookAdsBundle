<?php

namespace Werkspot\FacebookAdsBundle\Tests\Api;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AsyncJobInsights;
use FacebookAds\Object\Campaign;
use Mockery;
use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Api\Client;
use Werkspot\FacebookAdsBundle\Api\Exception\InsightsTimeoutException;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\Field;
use Werkspot\FacebookAdsBundle\Model\Insight\Params;

/**
 * @group time-sensitive
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    const MOCKED_RESULTS = 'mockedResult';
    const CAMPAIGN_ID = '<Campaign Id>';
    const APP_ID = 123;
    const APP_SECRET = 'S0me$ecr3t';
    const ACCESS_TOKEN = '0c59f7e609b0cc467067e39d523116ce';

    public function testGetInsights()
    {
        $api = $this->getApi(self::APP_ID, self::APP_SECRET, self::ACCESS_TOKEN);
        $params = $this->getParams();
        $this->mockCampaign($params, $this->getAsyncJobInsightsMock());
        $result = $api->getInsights(self::CAMPAIGN_ID, $params);
        $this->assertEquals(self::MOCKED_RESULTS, $result);
    }

    public function testGetInsightsTimeout()
    {
        $this->expectException(InsightsTimeoutException::class);

        $asyncJobInsightsMock = Mockery::mock(AsyncJobInsights::class);
        $asyncJobInsightsMock
            ->shouldReceive('read')
            ->shouldReceive('isComplete')->andReturn(false);

        $api = $this->getApi(self::APP_ID, self::APP_SECRET, self::ACCESS_TOKEN);
        $params = $this->getParams();
        $this->mockCampaign($params, $asyncJobInsightsMock);
        $result = $api->getInsights(self::CAMPAIGN_ID, $params);
        $this->assertEquals(self::MOCKED_RESULTS, $result);
    }

    public function testGetAdSetFromAccount()
    {
        $params = [0 => 'zero', 1 => 'one'];
        $result = ['result' => true];
        $accountMock = Mockery::mock(AdAccount::class);
        $accountMock
            ->shouldReceive('getAdSets')->with($params)
            ->andReturn($result);

        $api = $this->getApi(self::APP_ID, self::APP_SECRET, self::ACCESS_TOKEN);
        $apiResult = $api->getAdSetFromAccount($accountMock, $params);
        $this->assertEquals($result, $apiResult);
    }

    private function getAsyncJobInsightsMock()
    {
        $asyncJobInsightsMock = Mockery::mock(AsyncJobInsights::class);
        $asyncJobInsightsMock
            ->shouldReceive('read')->twice()
            ->shouldReceive('isComplete')->andReturn(false, false, true)
            ->shouldReceive('getResult')->andReturn(self::MOCKED_RESULTS);

        return $asyncJobInsightsMock;
    }

    /**
     * @param $appId
     * @param $appSecret
     * @param $accessToken
     *
     * @return Client
     */
    private function getApi($appId, $appSecret, $accessToken)
    {
        $facebookApi = \Mockery::mock('overload:' . Api::class);
        $facebookApi->shouldReceive('init')->with($appId, $appSecret, $accessToken);

        return new Client($appId, $appSecret, $accessToken);
    }

    /**
     * @param Params $params
     * @param $asyncJobInsightsMock
     */
    private function mockCampaign(Params $params, $asyncJobInsightsMock)
    {
        $campaignMock = \Mockery::mock('overload:' . Campaign::class);
        $campaignMock->shouldReceive('getInsightsAsync')
            ->with([], $params->getParamsArray())
            ->andReturn($asyncJobInsightsMock);
    }

    /**
     * @return Params
     */
    private function getParams()
    {
        $params = new Params();
        $params->addField(Field::get(Field::ACTIONS));

        return $params;
    }
}
