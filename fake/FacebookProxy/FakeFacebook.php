<?php
namespace WerkspotFake\FacebookAdsBundle\FacebookProxy;

use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClientInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookInterface;

class FakeFacebook implements FacebookInterface
{
    public function getClient(): FacebookClientInterface
    {
        return new FakeFacebookClient();
    }

}
