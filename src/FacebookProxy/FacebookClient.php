<?php
namespace Werkspot\FacebookAdsBundle\FacebookProxy;

use Facebook\FacebookClient as RealFacebookClient;

class FacebookClient extends RealFacebookClient implements FacebookClientInterface {}
