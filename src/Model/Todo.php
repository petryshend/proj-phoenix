<?php

namespace App\Model;

class Todo
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly bool $done,
    ) {}
}
