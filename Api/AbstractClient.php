<?php

namespace Werkspot\FacebookAdsBundle\Api;

use FacebookAds\Api;

abstract class AbstractClient
{
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
}
