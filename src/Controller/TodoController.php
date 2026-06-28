<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TodoController extends AbstractController
{
    private const PER_PAGE = 5;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TodoRepository $todoRepository,
    ) {}

    #[Route('/todo', name: 'todo_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $total = $this->todoRepository->count([]);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $request->query->getInt('page', 1)), $totalPages);

        return $this->render('todo/index.html.twig', [
            'todos' => $this->todoRepository->findSortedPage($page, self::PER_PAGE),
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/todo/new', name: 'todo_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $title = trim((string) $request->request->get('title', ''));

        if ($title !== '') {
            $this->em->persist(new Todo($title));
            $this->em->flush();
            $this->addFlash('success', 'Todo added.');
        }

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/todo/{id}/toggle', name: 'todo_toggle', methods: ['POST'])]
    public function toggle(Todo $todo): Response
    {
        $todo->toggle();
        $this->em->flush();
        $this->addFlash('success', $todo->isDone() ? 'Marked as done.' : 'Marked as pending.');

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/todo/{id}/delete', name: 'todo_delete', methods: ['POST'])]
    public function delete(Todo $todo): Response
    {
        $this->em->remove($todo);
        $this->em->flush();
        $this->addFlash('success', 'Todo deleted.');

        return $this->redirectToRoute('todo_index');
    }
}
