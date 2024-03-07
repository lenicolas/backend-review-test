<?php

namespace App\Repository;

use App\Dto\SearchInput;

interface ReadEventRepositoryInterface
{
    public function countAll(SearchInput $searchInput): int;

    /**
     * @param SearchInput $searchInput
     * @return array<\Iterator>
     */
    public function countByType(SearchInput $searchInput): array;

    /**
     * @param SearchInput $searchInput
     * @return array<int|string, mixed>>
     */
    public function statsByTypePerHour(SearchInput $searchInput): array;

    /**
     * @param SearchInput $searchInput
     * @return array<int, array<string, mixed>>
     */
    public function getLatest(SearchInput $searchInput): array;

    public function exist(int $id): bool;
}
