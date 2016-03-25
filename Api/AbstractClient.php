<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Facebook\Facebook;
use FacebookAds\Api;

abstract class AbstractClient
{
    const GRAPH_VERSION = 'v2.5';

    /**
     * @var int
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * AbstractClient constructor.
     *
     * @param int $appId
     * @param string $appSecret
     * @param string $accessToken
     */
    public function __construct($appId, $appSecret, $accessToken)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->accessToken = $accessToken;
    }

    protected function initApi()
    {
        Api::init($this->appId, $this->appSecret, $this->accessToken);
    }

    protected function initFacebook()
    {
        $facebook = new Facebook(
            [
                'app_id' => $this->appId,
                'app_secret' => $this->appSecret,
                'default_graph_version' => self::GRAPH_VERSION
            ]
        );
        $facebook->setDefaultAccessToken($this->accessToken);
        return $facebook;
    }
}
