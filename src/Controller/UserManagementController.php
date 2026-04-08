<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user-management')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'app_admin_user_management_index')]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('UserManagementFolder/index.html.twig', [
            'users' => $users,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_management_new')]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Validate that password is provided for new users
            if (!$plainPassword) {
                $this->addFlash('error', 'Password is required for new users.');
                return $this->render('UserManagementFolder/new.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            // Auto-verify users created by admin
            $user->setIsVerified(true);
            $user->setVerificationToken(null); // No verification token needed

            $entityManager->persist($user);
            $entityManager->flush();

            // Log the user creation activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('CREATE');
            $activityLog->setTargetData('User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ') - Role: ' . implode(', ', $user->getRoles()) . ' - Auto-verified');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'User created successfully and automatically verified!');

            return $this->redirectToRoute('app_admin_user_management_index');
        }

        return $this->render('UserManagementFolder/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_management_edit')]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
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

            $entityManager->flush();

            // Log the user edit activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('UPDATE');
            $activityLog->setTargetData('User: ' . $user->getUsername() . ' (ID: ' . $user->getId() . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');

            return $this->redirectToRoute('app_admin_user_management_index');
        }

        return $this->render('UserManagementFolder/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_user_management_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $username = $user->getUsername();
            $userId = $user->getId();

            $entityManager->remove($user);
            $entityManager->flush();

            // Log the user deletion activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('DELETE');
            $activityLog->setTargetData('User: ' . $username . ' (ID: ' . $userId . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('app_admin_user_management_index');
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_user_management_toggle_status')]
    public function toggleStatus(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(!$user->isActive());
        $entityManager->flush();

        $status = $user->isActive() ? 'activated' : 'deactivated';
        
        // Log the status change activity
        $activityLog = new ActivityLog();
        $activityLog->setUser($this->getUser());
        $activityLog->setUsername($this->getUser()->getUserIdentifier());
        $activityLog->setRole(json_encode($this->getUser()->getRoles()));
        $activityLog->setAction('UPDATE');
        $activityLog->setTargetData('User ' . $user->getUsername() . ' (ID: ' . $user->getId() . ') ' . $status);
        $entityManager->persist($activityLog);
        $entityManager->flush();

        $this->addFlash('success', 'User ' . $status . ' successfully!');

        return $this->redirectToRoute('app_admin_user_management_index');
    }
}
