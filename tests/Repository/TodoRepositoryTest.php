<?php

namespace App\Tests\Repository;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TodoRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private TodoRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema([$this->em->getClassMetadata(Todo::class)]);

        $this->repository = $this->em->getRepository(Todo::class);
    }

    public function testActivePageReturnsOnlyPendingItems(): void
    {
        $pending = $this->createTodo('Pending task', new \DateTimeImmutable('2026-01-01'));
        $this->createTodo('Done task', new \DateTimeImmutable('2026-01-03'), true, new \DateTimeImmutable('2026-01-04'));

        $results = $this->repository->findByDoneSortedPage(false, 1, 5);

        $this->assertCount(1, $results);
        $this->assertSame($pending->getId(), $results[0]->getId());
    }

    public function testDonePageReturnsOnlyDoneItems(): void
    {
        $this->createTodo('Pending task', new \DateTimeImmutable('2026-01-01'));
        $done = $this->createTodo('Done task', new \DateTimeImmutable('2026-01-03'), true, new \DateTimeImmutable('2026-01-04'));

        $results = $this->repository->findByDoneSortedPage(true, 1, 5);

        $this->assertCount(1, $results);
        $this->assertSame($done->getId(), $results[0]->getId());
    }

    public function testPendingItemsSortedByCreatedAtDescending(): void
    {
        $older = $this->createTodo('Older task', new \DateTimeImmutable('2026-01-01'));
        $newer = $this->createTodo('Newer task', new \DateTimeImmutable('2026-01-03'));
        $middle = $this->createTodo('Middle task', new \DateTimeImmutable('2026-01-02'));

        $results = $this->repository->findByDoneSortedPage(false, 1, 5);

        $this->assertSame($newer->getId(), $results[0]->getId());
        $this->assertSame($middle->getId(), $results[1]->getId());
        $this->assertSame($older->getId(), $results[2]->getId());
    }

    public function testDoneItemsSortedByDoneAtDescending(): void
    {
        $doneFirst = $this->createTodo('Done first', new \DateTimeImmutable('2026-01-01'), true, new \DateTimeImmutable('2026-01-02'));
        $doneLast = $this->createTodo('Done last', new \DateTimeImmutable('2026-01-01'), true, new \DateTimeImmutable('2026-01-04'));
        $doneMiddle = $this->createTodo('Done middle', new \DateTimeImmutable('2026-01-01'), true, new \DateTimeImmutable('2026-01-03'));

        $results = $this->repository->findByDoneSortedPage(true, 1, 5);

        $this->assertSame($doneLast->getId(), $results[0]->getId());
        $this->assertSame($doneMiddle->getId(), $results[1]->getId());
        $this->assertSame($doneFirst->getId(), $results[2]->getId());
    }

    public function testFindByDoneSortedPageReturnsOnlyRequestedPageSize(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $this->createTodo("Task $i", new \DateTimeImmutable("2026-01-0$i"));
        }

        $results = $this->repository->findByDoneSortedPage(false, 1, 5);

        $this->assertCount(5, $results);
    }

    private function createTodo(
        string $title,
        \DateTimeImmutable $createdAt,
        bool $done = false,
        ?\DateTimeImmutable $doneAt = null,
    ): Todo {
        $todo = new Todo($title);

        $ref = new \ReflectionClass($todo);

        $ref->getProperty('createdAt')->setValue($todo, $createdAt);

        if ($done) {
            $ref->getProperty('done')->setValue($todo, true);
            $ref->getProperty('doneAt')->setValue($todo, $doneAt);
        }

        $this->em->persist($todo);
        $this->em->flush();

        return $todo;
    }
}
