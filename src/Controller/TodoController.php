<?php

namespace App\Controller;

use App\Model\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo_index')]
    public function index(): Response
    {
        $todos = [
            new Todo(1, 'Buy groceries', true),
            new Todo(2, 'Walk the dog', false),
            new Todo(3, 'Read a book', false),
            new Todo(4, 'Write unit tests', true),
            new Todo(5, 'Deploy to production', false),
        ];

        return $this->render('todo/index.html.twig', ['todos' => $todos]);
    }
}
