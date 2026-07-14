<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
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

    #[Route('/', name: 'todo_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->renderList($request, done: false, route: 'todo_index', heading: 'Active');
    }

    #[Route('/done', name: 'todo_done', methods: ['GET'])]
    public function done(Request $request): Response
    {
        return $this->renderList($request, done: true, route: 'todo_done', heading: 'Done');
    }

    private function renderList(Request $request, bool $done, string $route, string $heading): Response
    {
        $user = $this->getCurrentUser();

        $activeCount = $this->todoRepository->countByDone(false, $user);
        $doneCount = $this->todoRepository->countByDone(true, $user);
        $total = $done ? $doneCount : $activeCount;
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min(max(1, $request->query->getInt('page', 1)), $totalPages);

        return $this->render('todo/list.html.twig', [
            'todos' => $this->todoRepository->findByDoneSortedPage($done, $user, $page, self::PER_PAGE),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'route' => $route,
            'heading' => $heading,
            'activeCount' => $activeCount,
            'doneCount' => $doneCount,
        ]);
    }

    #[Route('/todo/new', name: 'todo_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $this->assertCsrf($request, 'todo_new');

        $title = trim((string) $request->request->get('title', ''));

        if ($title !== '') {
            $this->em->persist(new Todo($title, $this->getCurrentUser()));
            $this->em->flush();
            $this->addFlash('success', 'Todo added.');
        }

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/todo/{id}/toggle', name: 'todo_toggle', methods: ['POST'])]
    public function toggle(Request $request, Todo $todo): Response
    {
        $this->assertCsrf($request, 'todo_toggle');
        $this->assertOwned($todo);

        $todo->toggle();
        $this->em->flush();
        $this->addFlash('success', $todo->isDone() ? 'Marked as done.' : 'Marked as pending.');

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/todo/{id}/delete', name: 'todo_delete', methods: ['POST'])]
    public function delete(Request $request, Todo $todo): Response
    {
        $this->assertCsrf($request, 'todo_delete');
        $this->assertOwned($todo);

        $this->em->remove($todo);
        $this->em->flush();
        $this->addFlash('success', 'Todo deleted.');

        return $this->redirectToRoute('todo_index');
    }

    private function getCurrentUser(): User
    {
        $user = $this->getUser();
        \assert($user instanceof User);

        return $user;
    }

    private function assertOwned(Todo $todo): void
    {
        if ($todo->getOwner() !== $this->getCurrentUser()) {
            throw $this->createAccessDeniedException('You do not own this todo.');
        }
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
    }
}
