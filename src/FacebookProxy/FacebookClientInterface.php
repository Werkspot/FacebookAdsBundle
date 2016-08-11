<?php
namespace Werkspot\FacebookAdsBundle\FacebookProxy;

use Facebook\FacebookRequest;
use Facebook\FacebookResponse;

interface FacebookClientInterface
{
    /**
     * @param FacebookRequest $request
     * @return FacebookResponse
     */
    public function sendRequest(FacebookRequest $request);
}
