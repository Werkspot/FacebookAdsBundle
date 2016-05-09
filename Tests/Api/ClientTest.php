<?php
namespace Werkspot\FacebookAdsBundle\Tests\Api;

use Facebook\Facebook;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AsyncJobInsights;
use FacebookAds\Object\Campaign;
use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Api\Client;
use Werkspot\FacebookAdsBundle\Api\Exception\InsightsTimeoutException;
use Werkspot\FacebookAdsBundle\Model\Batch;
use Werkspot\FacebookAdsBundle\Model\Insight\Enum\Field;
use Werkspot\FacebookAdsBundle\Model\Insight\Params as InsightsParams;
use Werkspot\FacebookAdsBundle\Model\AdSet\Params as AdSetParams;

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
        $params = $this->getInsightsParams();
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
        $params = $this->getInsightsParams();
        $this->mockCampaign($params, $asyncJobInsightsMock);
        $result = $api->getInsights(self::CAMPAIGN_ID, $params);
        $this->assertEquals(self::MOCKED_RESULTS, $result);
    }

    public function testGetAdSetFromAccount()
    {
        $params = $this->getAdSetParams();
        $accountId = 12345678;
        $result = ['result' => true];
        $accountMock = Mockery::mock('overload:' . AdAccount::class);
        $accountMock
            ->shouldReceive('getAdSets')->with($params->getParamsArray())
            ->andReturn($result);

        $api = $this->getApi(self::APP_ID, self::APP_SECRET, self::ACCESS_TOKEN);
        $apiResult = $api->getAdSetFromAccount($accountId, $params);
        $this->assertEquals($result, $apiResult);
    }

    public function testGetFromBatch()
    {
        $returnedData = ['Some Data', 'That' => ['gets' => 'returned']];
        $responseMock =  Mockery::mock(\stdClass::class);
        $responseMock->shouldReceive('getBody')->once()->andReturn(json_encode($returnedData));

        $request =  new Batch\Request();
        $batch = new Batch();
        $batch->addRequests($request);
        $api = $this->getApi(self::APP_ID, self::APP_SECRET, self::ACCESS_TOKEN);

        $facebookMock = Mockery::mock('overload:' . Facebook::class);
        $facebookMock
            ->shouldReceive('setDefaultAccessToken')
            ->withArgs([self::ACCESS_TOKEN])
            ->once();
        $facebookMock
            ->shouldReceive('post')
            ->withArgs(['/', ['batch' => json_encode($batch->getArray())]])
            ->once()
            ->andReturn($responseMock);

        $this->assertEquals($returnedData, $api->getFromBatch($batch));

    }

    private function getAsyncJobInsightsMock()
    {
        $asyncJobInsightsMock = Mockery::mock(AsyncJobInsights::class);
        $asyncJobInsightsMock
            ->shouldReceive('read')
            ->twice();
        $asyncJobInsightsMock
            ->shouldReceive('isComplete')
            ->andReturn(false, false, true);
        $asyncJobInsightsMock
            ->shouldReceive('getResult')
            ->andReturn(self::MOCKED_RESULTS);

        return $asyncJobInsightsMock;
    }

    /**
     * @param int $appId
     * @param string $appSecret
     * @param string $accessToken
     *
     * @return Client
     */
    private function getApi($appId, $appSecret, $accessToken)
    {
        $facebookApi = \Mockery::mock('overload:' . Api::class);
        $facebookApi
            ->shouldReceive('init')
            ->with($appId, $appSecret, $accessToken);

        return new Client($appId, $appSecret, $accessToken);
    }

    /**
     * @param InsightsParams $params
     * @param MockInterface $asyncJobInsightsMock
     */
    private function mockCampaign(InsightsParams $params, MockInterface $asyncJobInsightsMock)
    {
        $campaignMock = \Mockery::mock('overload:' . Campaign::class);
        $campaignMock->shouldReceive('getInsightsAsync')
            ->with([], $params->getParamsArray())
            ->andReturn($asyncJobInsightsMock);
    }

    /**
     * @return InsightsParams
     */
    private function getInsightsParams()
    {
        $params = new InsightsParams();
        $params->addField(Field::get(Field::ACTIONS));

        return $params;
    }

    /**
     * @return AdSetParams
     */
    private function getAdSetParams()
    {
        $params = new AdSetParams();
        $params->addAllFields();

        return $params;
    }
}
