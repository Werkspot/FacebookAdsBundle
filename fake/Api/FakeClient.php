<?php
namespace WerkspotFake\FacebookAdsBundle\Api;

use Werkspot\FacebookAdsBundle\Api\ClientInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookAppInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClient;
use WerkspotFake\FacebookAdsBundle\FacebookProxy\FakeFacebookApp;

class FakeClient implements ClientInterface
{
    const ACCESS_TOKEN = 'accesstoken';
    public function getFacebookApp(): FacebookAppInterface
    {
        return new FakeFacebookApp();
    }

    public function getFacebookClient(): FacebookClient
    {
        return (new FakeFacebook())->getClient();
    }

    public function getAccessToken(): string
    {
        return self::ACCESS_TOKEN;
    }

}
