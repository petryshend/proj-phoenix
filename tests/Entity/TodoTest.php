<?php

namespace App\Tests\Entity;

use App\Entity\Todo;
use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{
    public function testConstructorSetsTitle(): void
    {
        $todo = new Todo('Buy groceries');

        $this->assertSame('Buy groceries', $todo->getTitle());
    }

    public function testConstructorSetsDoneToFalse(): void
    {
        $todo = new Todo('Buy groceries');

        $this->assertFalse($todo->isDone());
    }

    public function testConstructorSetsCreatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $todo = new Todo('Buy groceries');
        $after = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $todo->getCreatedAt());
        $this->assertLessThanOrEqual($after, $todo->getCreatedAt());
    }

    public function testConstructorSetsDoneAtToNull(): void
    {
        $todo = new Todo('Buy groceries');

        $this->assertNull($todo->getDoneAt());
    }

    public function testToggleMarksTodoAsDone(): void
    {
        $todo = new Todo('Buy groceries');
        $todo->toggle();

        $this->assertTrue($todo->isDone());
    }

    public function testToggleSetsDateWhenDone(): void
    {
        $todo = new Todo('Buy groceries');
        $todo->toggle();

        $this->assertInstanceOf(\DateTimeImmutable::class, $todo->getDoneAt());
    }

    public function testToggleMarksTodoAsPending(): void
    {
        $todo = new Todo('Buy groceries');
        $todo->toggle();
        $todo->toggle();

        $this->assertFalse($todo->isDone());
    }

    public function testToggleClearsDoneAtWhenPending(): void
    {
        $todo = new Todo('Buy groceries');
        $todo->toggle();
        $todo->toggle();

        $this->assertNull($todo->getDoneAt());
    }
}
