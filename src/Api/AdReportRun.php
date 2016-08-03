<?php
namespace Werkspot\FacebookAdsBundle\Api;

use DateTime;
use Facebook\FacebookRequest;

class AdReportRun
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
    private $isRunning;
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

    private function fetchData(): void
    {
        $client =  $this->client;

        $request = new FacebookRequest(
            $client->getFacebookApp(),
            $client->getAccessToken(),
            'GET',
            "/{$this->id}/"
        );
        $response = $client->getFacebookClient()->sendRequest($request);
        foreach ($response as $key => $value) {
            $key = $this->snakeToCamelCase($key);

            if (property_exists($this, $key)) {
                $this->{$key} = $this->convertDateTime($key, $value);
            }
        }
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getIsBookmarked(): bool
    {
        return $this->isBookmarked;
    }

    public function getIsRunning(): bool
    {
        return $this->isRunning;
    }

    public function getScheduleId(): bool
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

    private function convertDateTime(string $key, mixed $value)
    {
        if (strpos($key, 'date') !== false) {
            return new DateTime($value);
        }

        if (strpos($key, 'time') !== false) {
            return (new DateTime())->setTimestamp($value);
        }

        return $value;
    }
}
