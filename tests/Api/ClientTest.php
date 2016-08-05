<?php declare(strict_types=1);
namespace WerkspotTest\FacebookAdsBundle\tests\Api;

use Facebook\FacebookClient;
use PHPUnit_Framework_TestCase;
use Werkspot\FacebookAdsBundle\Api\Client;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookApp;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $appId = '12';
        $appSecret = 'secret';
        $accessToken = 'assestoken';

        $client = new Client($appId, $appSecret, $accessToken);
        $this->assertSame($accessToken, $client->getAccessToken());

        $this->assertInstanceOf(FacebookApp::class, $client->getFacebookApp());
        $this->assertInstanceOf(FacebookClient::class, $client->getFacebookClient());
    }
}
