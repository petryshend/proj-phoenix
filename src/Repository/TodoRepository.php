<?php

namespace App\Repository;

use App\Entity\Todo;
use App\Entity\User;
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
    public function findByDoneSortedPage(bool $done, User $owner, int $page, int $perPage): array
    {
        // Most recent first: pending items by createdAt, done items by doneAt.
        return $this->createByDoneQueryBuilder($done, $owner)
            ->orderBy($done ? 't.doneAt' : 't.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
    }

    public function countByDone(bool $done, User $owner): int
    {
        return (int) $this->createByDoneQueryBuilder($done, $owner)
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Items owned by the given user, filtered by their done state. Ordering is
     * left to callers: a COUNT() query must not carry an ORDER BY on a
     * non-aggregated column, which PostgreSQL rejects.
     */
    private function createByDoneQueryBuilder(bool $done, User $owner): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.done = :done')
            ->andWhere('t.owner = :owner')
            ->setParameter('done', $done)
            ->setParameter('owner', $owner);
    }
}
