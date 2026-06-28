<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        $pending = $this->createQueryBuilder('t')
            ->where('t.done = false')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $done = $this->createQueryBuilder('t')
            ->where('t.done = true')
            ->orderBy('t.doneAt', 'DESC')
            ->getQuery()
            ->getResult();

        return [...$pending, ...$done];
    }
}
