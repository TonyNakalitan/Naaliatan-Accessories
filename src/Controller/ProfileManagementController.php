<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileManagementController extends AbstractController
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    // Admin routes
    #[Route('/admin/profile', name: 'app_admin_profile_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(): Response
    {
        return $this->show();
    }

    #[Route('/admin/profile/edit', name: 'app_admin_profile_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminEdit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $userPasswordHasher, $entityManager, 'admin');
    }

    // Staff routes
    #[Route('/staff/profile', name: 'app_staff_profile_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(): Response
    {
        return $this->show();
    }

    #[Route('/staff/profile/edit', name: 'app_staff_profile_edit')]
    #[IsGranted('ROLE_STAFF')]
    public function staffEdit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $userPasswordHasher, $entityManager, 'staff');
    }

    // Shared implementation methods
    private function show(): Response
    {
        return $this->render('ProfileManagementFolder/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    private function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, string $role): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password change if provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
            }

            // Handle profile picture upload
            $imageFile = $form->get('profilePicture')->getData();
            if ($imageFile) {
                // Delete old profile picture if exists
                if ($user->getProfilePicture()) {
                    $oldImagePath = $this->getParameter('profile_images_directory') . '/' . $user->getProfilePicture();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                    $user->setProfilePicture($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'There was an error uploading your profile picture.');
                }
            }

            $entityManager->flush();

            // Log the profile update activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($user);
            $activityLog->setUsername($user->getUserIdentifier());
            $activityLog->setRole(json_encode($user->getRoles()));
            $activityLog->setAction('UPDATE');
            $activityLog->setTargetData('Profile updated: ' . $user->getUserIdentifier());
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');

            $routeName = $role === 'admin' ? 'app_admin_profile_show' : 'app_staff_profile_show';
            return $this->redirectToRoute($routeName);
        }

        return $this->render('ProfileManagementFolder/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
