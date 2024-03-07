<?php

namespace App\Repository;

use App\Dto\EventInput;

class WriteEventRepository implements WriteEventRepositoryInterface
{
    public function update(EventInput $authorInput, int $id): void
    {
        $sql = <<<SQL
        UPDATE event
        SET comment = :comment
        WHERE id = :id
SQL;
        $this->

        $this->connection->executeQuery($sql, ['id' => $id, 'comment' => $authorInput->comment]);
    }
}
