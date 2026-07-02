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
    public function findByDoneSortedPage(bool $done, int $page, int $perPage): array
    {
        return $this->createByDoneQueryBuilder($done)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();
    }

    /**
     * Items filtered by their done state, most recent first: pending items are
     * ordered by createdAt, done items by doneAt.
     */
    private function createByDoneQueryBuilder(bool $done): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.done = :done')
            ->setParameter('done', $done)
            ->orderBy($done ? 't.doneAt' : 't.createdAt', 'DESC');
    }
}
