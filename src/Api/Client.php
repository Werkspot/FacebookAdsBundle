<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookApp;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClient;
use Werkspot\FacebookAdsBundle\FacebookProxy\Facebook;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookAppInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClientInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookInterface;

class Client implements ClientInterface
{
    const GRAPH_VERSION = 'v2.7';

    /** @var string */
    private $appId;

    /** @var string */
    private $appSecret;

    /** @var string */
    private $accessToken;

    public function __construct(string $appId, string $appSecret, string $accessToken)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
    }

    public function getFacebookApp(): FacebookAppInterface
    {
        return new FacebookApp($this->appId, $this->appSecret);
    }

    private function getFacebook(): FacebookInterface
    {
        return new Facebook(
            [
                'app_id' => $this->appId,
                'app_secret' => $this->appSecret,
                'default_graph_version' => self::GRAPH_VERSION
            ]
        );
    }

    public function getFacebookClient(): FacebookClientInterface
    {
        return $this->getFacebook()->getClient();
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

}
