<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Todo>
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    /** @return Todo[] */
    public function findAllSorted(): array
    {
        return $this->createSortedQueryBuilder()->getQuery()->getResult();
    }

    /** @return Todo[] */
    public function findSortedPage(int $page, int $perPage): array
    {
        return $this->createSortedQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
    }

    /**
     * Pending (done=false) items first, then done items; within each group the
     * most recent activity first. sortDate = doneAt for done items, createdAt
     * for pending items.
     */
    private function createSortedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->addSelect('CASE WHEN t.done = true THEN t.doneAt ELSE t.createdAt END AS HIDDEN sortDate')
            ->orderBy('t.done', 'ASC')
            ->addOrderBy('sortDate', 'DESC');
    }
}
