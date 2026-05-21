<?php

namespace App\Controller\Api;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ApiRegisterController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        EmailVerificationService $emailVerificationService,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $username = trim($data['username'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        // Basic validation
        if (!$username || !$email || !$password) {
            return $this->json(['message' => 'username, email, and password are required.'], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Invalid email address.'], 422);
        }

        if (strlen($password) < 6) {
            return $this->json(['message' => 'Password must be at least 6 characters.'], 422);
        }

        // Check for duplicate email
        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'An account with this email already exists.'], 409);
        }

        // Create user
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_CUSTOMER']);
        $user->setIsVerified(false);

        $verificationToken = $emailVerificationService->generateVerificationToken();
        $user->setVerificationToken($verificationToken);

        $entityManager->persist($user);
        $entityManager->flush();

        // Activity log
        $log = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUsername());
        $log->setRole(json_encode($user->getRoles()));
        $log->setAction('REGISTER');
        $log->setTargetData('API registration: ' . $user->getEmail());
        $entityManager->persist($log);
        $entityManager->flush();

        // Send verification email
        $emailSent = true;
        try {
            $verificationUrl = $this->generateUrl(
                'app_verify_email',
                ['token' => $verificationToken],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $emailVerificationService->sendVerificationEmail($user, $verificationUrl);
        } catch (\Exception $e) {
            $emailSent = false;
        }

        return $this->json([
            'message'    => 'Registration successful. Please verify your email.',
            'email_sent' => $emailSent,
            'user'       => [
                'id'         => $user->getId(),
                'username'   => $user->getUsername(),
                'email'      => $user->getEmail(),
                'roles'      => $user->getRoles(),
                'isVerified' => $user->isVerified(),
                'createdAt'  => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            ],
        ], 201);
    }
}
