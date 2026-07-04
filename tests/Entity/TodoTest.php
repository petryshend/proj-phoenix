<?php

namespace App\Tests\Entity;

use App\Entity\Todo;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{
    public function testConstructorSetsTitle(): void
    {
        $todo = $this->newTodo('Buy groceries');

        $this->assertSame('Buy groceries', $todo->getTitle());
    }

    public function testConstructorSetsOwner(): void
    {
        $owner = $this->newUser();
        $todo = new Todo('Buy groceries', $owner);

        $this->assertSame($owner, $todo->getOwner());
    }

    public function testConstructorSetsDoneToFalse(): void
    {
        $todo = $this->newTodo('Buy groceries');

        $this->assertFalse($todo->isDone());
    }

    public function testConstructorSetsCreatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $todo = $this->newTodo('Buy groceries');
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $todo->getCreatedAt());
        $this->assertLessThanOrEqual($after, $todo->getCreatedAt());
    }

    public function testConstructorSetsDoneAtToNull(): void
    {
        $todo = $this->newTodo('Buy groceries');

        $this->assertNull($todo->getDoneAt());
    }

    public function testToggleMarksTodoAsDone(): void
    {
        $todo = $this->newTodo('Buy groceries');
        $todo->toggle();

        $this->assertTrue($todo->isDone());
    }

    public function testToggleSetsDateWhenDone(): void
    {
        $todo = $this->newTodo('Buy groceries');
        $todo->toggle();

        $this->assertInstanceOf(\DateTimeImmutable::class, $todo->getDoneAt());
    }

    public function testToggleMarksTodoAsPending(): void
    {
        $todo = $this->newTodo('Buy groceries');
        $todo->toggle();
        $todo->toggle();

        $this->assertFalse($todo->isDone());
    }

    public function testToggleClearsDoneAtWhenPending(): void
    {
        $todo = $this->newTodo('Buy groceries');
        $todo->toggle();
        $todo->toggle();

        $this->assertNull($todo->getDoneAt());
    }

    private function newTodo(string $title): Todo
    {
        return new Todo($title, $this->newUser());
    }

    private function newUser(): User
    {
        $user = new User();
        $user->setEmail('owner@example.com');

        return $user;
    }
}
