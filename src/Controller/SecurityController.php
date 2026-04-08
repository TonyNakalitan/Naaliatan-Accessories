<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\EmailVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if ($user) {
            // Redirect to role-based dashboard
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_admin_dashboard');
            } else {
                return $this->redirectToRoute('app_staff_dashboard');
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('LoginFormFolder/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/logout-success', name: 'app_logout_success')]
    public function logoutSuccess(EntityManagerInterface $entityManager): Response
    {
        // This is called after successful logout
        // We need to get the user from session before it's cleared
        return $this->redirectToRoute('app_login');
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        EmailVerificationService $emailVerificationService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $username = $form->get('username')->getData();
            $email = $form->get('email')->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            // Validate that required fields are not null and are proper types
            if (!$username || !is_string($username) || trim($username) === '') {
                $this->addFlash('error', 'Username is required and must be a valid string.');
                return $this->render('RegisterFormFolder/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            if (!$email || !is_string($email) || trim($email) === '') {
                $this->addFlash('error', 'Email is required and must be a valid string.');
                return $this->render('RegisterFormFolder/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            if (!$plainPassword || !is_string($plainPassword) || trim($plainPassword) === '') {
                $this->addFlash('error', 'Password is required and must be a valid string.');
                return $this->render('RegisterFormFolder/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Clean and validate the data
            $username = trim($username);
            $email = trim($email);
            $plainPassword = trim($plainPassword);

            // Additional email validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
                return $this->render('RegisterFormFolder/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // Set user data with validated strings
            $user->setUsername($username);
            $user->setEmail($email);
            
            // Hash the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            // Set default role as staff for new registrations
            $user->setRoles(['ROLE_STAFF']);
            
            // User is not verified yet
            $user->setIsVerified(false);
            
            // Generate and set verification token
            $verificationToken = $emailVerificationService->generateVerificationToken();
            $user->setVerificationToken($verificationToken);

            $entityManager->persist($user);
            $entityManager->flush();

            // Log the registration activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($user);
            $activityLog->setUsername($user->getUsername());
            $activityLog->setRole(json_encode($user->getRoles()));
            $activityLog->setAction('REGISTER');
            $activityLog->setTargetData('New user registered: ' . $user->getEmail());
            $entityManager->persist($activityLog);
            $entityManager->flush();

            // Send verification email
            try {
                $verificationUrl = $this->generateUrl('app_verify_email', [
                    'token' => $verificationToken
                ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
                
                $emailVerificationService->sendVerificationEmail($user, $verificationUrl);

                $this->addFlash('success', 'Registration successful! Please check your email to verify your account.');
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Registration successful, but we couldn\'t send the verification email. Please contact support.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('RegisterFormFolder/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
