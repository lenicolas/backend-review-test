<?php

namespace App\Repository;

use App\Dto\SearchInput;

interface ReadEventRepositoryInterface
{
    public function countAll(SearchInput $searchInput): int;
    public function countByType(SearchInput $searchInput): array;
    public function statsByTypePerHour(SearchInput $searchInput): array;
    public function getLatest(SearchInput $searchInput): array;
    public function exist(int $id): bool;
}