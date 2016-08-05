<?php
namespace Werkspot\FacebookAdsBundle\Api\AdReportRun;

use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Werkspot\FacebookAdsBundle\Api\AdReportRun;

class Result implements ResultInterface
{
    private $adReportRun;
    private $data;

    public function __construct(AdReportRun $adReportRun)
    {
        $this->adReportRun = $adReportRun;
        $this->fetchResults();
    }

    private function fetchResults()
    {
        if ($this->adReportRun->isRunning()) {
            $this->adReportRun->fetchData();
            $this->fetchResults();
            return;
        }

        $client = $this->adReportRun->getClient();
        $request = new FacebookRequest(
            $client->getFacebookApp(),
            $client->getAccessToken(),
            'GET',
            "/{$this->adReportRun->getId()}/insights"
        );
        $this->parseData($client->getFacebookClient()->sendRequest($request));
    }

    private function parseData(FacebookResponse $response)
    {
        $this->data = $response->getDecodedBody();
    }

    public function getData(): array
    {
        return $this->data;
    }
}
