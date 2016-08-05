<?php
namespace Werkspot\FacebookAdsBundle\Api;

use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookAppInterface;
use Werkspot\FacebookAdsBundle\FacebookProxy\FacebookClient;

interface ClientInterface
{
    public function getFacebookApp(): FacebookAppInterface;
    public function getFacebookClient(): FacebookClient;
    public function getAccessToken(): string;
}
