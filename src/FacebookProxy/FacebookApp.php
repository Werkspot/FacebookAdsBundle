<?php
namespace Werkspot\FacebookAdsBundle\FacebookProxy;

use Facebook\FacebookApp as RealFacebookApp;

class FacebookApp extends RealFacebookApp implements FacebookAppInterface {}
