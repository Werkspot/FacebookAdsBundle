<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookAppInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClientInterface;

interface ClientInterface
{
    public function getFacebookApp(): FacebookAppInterface;
    public function getFacebookClient(): FacebookClientInterface;
    public function getAccessToken(): string;
}
