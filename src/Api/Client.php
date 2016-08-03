<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookClient;

class Client
{
    const GRAPH_VERSION = 'v2.7';

    /** @var int */
    private $appId;

    /** @var string */
    private $appSecret;

    /** @var string */
    private $accessToken;

    public function __construct(int $appId, string $appSecret, string $accessToken)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
    }

    public function getFacebookApp(): FacebookApp
    {
        return new FacebookApp($this->appId, $this->appSecret);
    }

    private function getFacebook(): Facebook
    {
        new Facebook(
            [
                'app_id' => $this->appId,
                'app_secret' => $this->appSecret,
                'default_graph_version' => self::GRAPH_VERSION
            ]
        );
    }

    public function getFacebookClient(): FacebookClient
    {
        return $this->getFacebook()->getClient();
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

}
