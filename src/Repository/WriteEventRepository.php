<?php

namespace App\Repository;

use App\Dto\EventInput;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;

class WriteEventRepository implements WriteEventRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(EventInput $eventInput, int $id): void
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);

        if ($event) {
            $event->setComment($eventInput->comment);
            $this->entityManager->flush();
        }
    }
}
