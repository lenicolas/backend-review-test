<?php

namespace App\Service;

interface GitHubEventsImporterServiceInterface
{
    public function importEvents(string $date, string $hour): void;
}
