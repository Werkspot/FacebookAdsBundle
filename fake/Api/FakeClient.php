<?php
namespace WerkspotFake\FacebookAdsBundle\Api;

use Werkspot\FacebookAdsBundle\Api\ClientInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookAppInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClientInterface;
use WerkspotFake\FacebookAdsBundle\FacebookProxy\FakeFacebook;
use WerkspotFake\FacebookAdsBundle\FacebookProxy\FakeFacebookApp;

class FakeClient implements ClientInterface
{
    const ACCESS_TOKEN = 'accesstoken';

    public function getFacebookApp(): FacebookAppInterface
    {
        return new FakeFacebookApp();
    }

    public function getFacebookClient(): FacebookClientInterface
    {
        return (new FakeFacebook())->getClient();
    }

    public function getAccessToken(): string
    {
        return self::ACCESS_TOKEN;
    }

}
