<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    private const MIN_PASSWORD_LENGTH = 6;

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        Security $security,
    ): Response {
        // Already signed in? Nothing to register.
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('todo_index');
        }

        $email = trim((string) $request->request->get('email', ''));

        if ($request->isMethod('POST')) {
            $password = (string) $request->request->get('password', '');
            $error = $this->validate($email, $password, $userRepository);

            if ($error === null && !$this->isCsrfTokenValid('register', (string) $request->request->get('_csrf_token'))) {
                $error = 'Invalid form submission. Please try again.';
            }

            if ($error === null) {
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $em->persist($user);
                $em->flush();

                $security->login($user);
                $this->addFlash('success', 'Welcome! Your account has been created.');

                return $this->redirectToRoute('todo_index');
            }

            $this->addFlash('error', $error);
        }

        return $this->render('security/register.html.twig', [
            'last_email' => $email,
        ]);
    }

    private function validate(string $email, string $password, UserRepository $userRepository): ?string
    {
        if ($email === '' || $password === '') {
            return 'Email and password are required.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }

        if (\strlen($password) < self::MIN_PASSWORD_LENGTH) {
            return sprintf('Password must be at least %d characters.', self::MIN_PASSWORD_LENGTH);
        }

        if ($userRepository->findOneBy(['email' => $email]) !== null) {
            return 'An account with this email already exists.';
        }

        return null;
    }
}
