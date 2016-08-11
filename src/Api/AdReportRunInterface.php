<?php
namespace Werkspot\FacebookAdsBundle\Api;

use DateTime;
use Werkspot\FacebookAdsBundle\Api\AdReportRun\ResultInterface;

interface AdReportRunInterface
{
    public function fetchTillComplete(): ResultInterface;
    public function getInsights(): ResultInterface;
    public function getId(): string;
    public function getClient(): Client;
    public function getAccountId(): string;
    public function getAsyncPercentCompletion(): int;
    public function getAsyncStatus(): string;
    public function getDateStart(): DateTime;
    public function getDateStop(): DateTime;
    public function getEmails(): array;
    public function getFriendlyName(): string;
    public function isBookmarked(): bool;
    public function isRunning(): bool;
    public function getScheduleId(): string;
    public function getTimeCompleted(): DateTime;
    public function getTimeRef(): DateTime;
}
