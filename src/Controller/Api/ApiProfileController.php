<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/profile')]
#[IsGranted('ROLE_CUSTOMER')]
class ApiProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('', name: 'api_profile_show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        return $this->json($this->serializeUser($this->getUser()));
    }

    #[Route('', name: 'api_profile_update', methods: ['PATCH'])]
    public function update(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true) ?? [];

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $displayName = trim((string) ($data['display_name'] ?? ''));
        $bio = trim((string) ($data['bio'] ?? ''));
        $zodiacSign = trim((string) ($data['zodiac_sign'] ?? ''));
        $newPassword = trim((string) ($data['new_password'] ?? ''));
        $confirmPassword = trim((string) ($data['confirm_password'] ?? ''));

        if ($displayName !== '') {
            $user->setDisplayName($displayName);
        }

        if ($bio !== '') {
            $user->setBio($bio);
        }

        if ($zodiacSign !== '') {
            $user->setZodiacSign($zodiacSign);
        }

        if ($newPassword !== '') {
            if ($newPassword !== $confirmPassword) {
                return $this->json(['message' => 'Passwords do not match.'], 422);
            }

            if (strlen($newPassword) < 6) {
                return $this->json(['message' => 'Password must be at least 6 characters.'], 422);
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        }

        $this->entityManager->flush();

        return $this->json($this->serializeUser($user));
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'displayName' => $user->getDisplayName(),
            'bio' => $user->getBio(),
            'zodiacSign' => $user->getZodiacSign(),
            'roles' => $user->getRoles(),
            'isActive' => $user->isActive(),
            'isVerified' => $user->isVerified(),
            'createdAt' => $user->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
