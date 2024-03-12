<?php

namespace App\Repository;

use App\Dto\EventInput;

interface WriteEventRepositoryInterface
{
    public function update(EventInput $authorInput, int $id): void;
}
