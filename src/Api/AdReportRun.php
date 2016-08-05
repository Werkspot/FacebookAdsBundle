<?php
namespace Werkspot\FacebookAdsBundle\Api;

use DateTime;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\Result;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\ResultInterface;

class AdReportRun implements AdReportRunInterface
{
    private $id;
    private $accountId;
    private $asyncPercentCompletion;
    private $asyncStatus;
    private $dateStart;
    private $dateStop;
    private $emails;
    private $friendlyName;
    private $isBookmarked;
    private $isRunning = true;
    private $scheduleId;
    private $timeCompleted;
    private $timeRef;
    private $client;

    public function __construct(string $id, Client $client)
    {
        $this->id = $id;
        $this->client = $client;
        $this->fetchData();
    }

    public function fetchData()
    {
        $client = $this->client;
        $request = new FacebookRequest($client->getFacebookApp(), $client->getAccessToken(), 'GET', "/{$this->id}/");
        $this->parseData($client->getFacebookClient()->sendRequest($request));
    }

    public function fetchTillComplete(): ResultInterface
    {
        do {
            $this->fetchData();
            sleep(1);
        } while ($this->asyncPercentCompletion!==100);

        return new Result($this);
    }

    public function getInsights(): ResultInterface
    {
        if ($this->asyncPercentCompletion!==100){
            return $this->fetchTillComplete();
        }

        return new Result($this);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getAsyncPercentCompletion(): int
    {
        return $this->asyncPercentCompletion;
    }

    public function getAsyncStatus(): string
    {
        return $this->asyncStatus;
    }

    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    public function getDateStop(): DateTime
    {
        return $this->dateStop;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getFriendlyName(): string
    {
        return $this->friendlyName;
    }

    public function isBookmarked(): bool
    {
        return $this->isBookmarked;
    }

    public function isRunning(): bool
    {
        if ($this->asyncPercentCompletion == 100) {
            return false;
        }
        return $this->isRunning;
    }

    public function getScheduleId(): string
    {
        return $this->scheduleId;
    }

    public function getTimeCompleted(): DateTime
    {
        return $this->timeCompleted;
    }

    public function getTimeRef(): DateTime
    {
        return $this->timeRef;
    }

    private function snakeToCamelCase(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
    }

    private function convertDateTime(string $key, $value)
    {
        if (strpos($key, 'date') !== false) {
            return new DateTime($value);
        }

        if (strpos($key, 'time') !== false) {
            return (new DateTime())->setTimestamp($value);
        }

        return $value;
    }

    private function parseData(FacebookResponse $response)
    {
        foreach ($response->getDecodedBody() as $key => $value) {
            $key = $this->snakeToCamelCase($key);

            if (property_exists($this, $key)) {
                $this->{$key} = $this->convertDateTime($key, $value);
            }
        }
    }
}
