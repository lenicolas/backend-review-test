<?php

namespace App\Repository;

use App\Dto\SearchInput;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class ReadEventRepository implements ReadEventRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function countAll(SearchInput $searchInput): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('count(e.id)')
            ->from(Event::class, 'e')
            ->where('e.createAt LIKE :date')
            ->andWhere('e.payload LIKE :keyword')
            ->setParameter('date', $searchInput->date->format('Y-m-d'))
            ->setParameter('keyword', '%' . $searchInput->keyword . '%');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
    public function countByType(SearchInput $searchInput): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e.type, sum(e.count) as count')
            ->from(Event::class, 'e')
            ->where('e.createAt LIKE :date')
            ->andWhere('e.payload LIKE :keyword')
            ->groupBy('e.type')
            ->setParameter('date', $searchInput->date->format('Y-m-d'))
            ->setParameter('keyword', '%' . $searchInput->keyword . '%');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function statsByTypePerHour(SearchInput $searchInput): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('hour', 'hour');
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('count', 'count');

        $sql = <<<SQL
            SELECT extract(hour from create_at) as hour, type, sum(count) as count
            FROM event
            WHERE date(create_at) = :date
            AND payload::text like :keyword
            GROUP BY TYPE, EXTRACT(hour from create_at)
SQL;

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('date', $searchInput->date->format('Y-m-d'));
        $query->setParameter('keyword', '%' . $searchInput->keyword . '%');

        $stats = $query->getArrayResult();

        $data = array_fill(0, 24, ['commit' => 0, 'pullRequest' => 0, 'comment' => 0]);

        foreach ($stats as $stat) {
            $data[(int) $stat['hour']][$stat['type']] = $stat['count'];
        }

        return $data;
    }

    public function getLatest(SearchInput $searchInput): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('e.type, e.repo')
            ->from(Event::class, 'e')
            ->where('e.createAt LIKE :date')
            ->andWhere('e.payload LIKE :keyword')
            ->setParameter('date', $searchInput->date->format('Y-m-d'))
            ->setParameter('keyword', '%' . $searchInput->keyword . '%');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function exist(int $id): bool
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        return $event !== null;
    }
}
