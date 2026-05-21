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
use Symfony\Component\HttpFoundation\JsonResponse;

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
            } elseif (in_array('ROLE_STAFF', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_staff_dashboard');
            } elseif (in_array('ROLE_CUSTOMER', $user->getRoles(), true)) {
                return $this->redirectToRoute('app_customer_dashboard');
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

            // Set default role as customer for new registrations
            $user->setRoles(['ROLE_CUSTOMER']);
            
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

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        EmailVerificationService $emailVerificationService
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            
            // Validate email
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
                return $this->redirectToRoute('app_forgot_password');
            }

            // Find user by email
            $user = $userRepository->findOneBy(['email' => $email]);
            
            if ($user) {
                // Generate reset token
                $resetToken = bin2hex(random_bytes(32));
                $user->setResetToken($resetToken);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                
                $entityManager->flush();
                
                // Send reset email
                try {
                    $resetUrl = $this->generateUrl('app_reset_password', [
                        'token' => $resetToken
                    ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
                    
                    // Send the reset email
                    $emailVerificationService->sendResetEmail($user, $resetUrl);
                    $this->addFlash('success', 'Password reset instructions have been sent to your email address.');
                    
                    // Log the password reset request
                    $activityLog = new ActivityLog();
                    $activityLog->setUser($user);
                    $activityLog->setUsername($user->getUsername());
                    $activityLog->setRole(json_encode($user->getRoles()));
                    $activityLog->setAction('PASSWORD_RESET_REQUEST');
                    $activityLog->setTargetData('Password reset requested for: ' . $user->getEmail());
                    $entityManager->persist($activityLog);
                    $entityManager->flush();
                    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'There was a problem sending the reset email. Please try again later.');
                }
            } else {
                // Don't reveal if email exists or not for security
                $this->addFlash('success', 'If an account with that email exists, password reset instructions have been sent.');
            }
            
            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('PasswordResetFolder/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Find user by reset token
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        
        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Invalid or expired reset token. Please request a new password reset.');
            return $this->redirectToRoute('app_forgot_password');
        }
        
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            
            // Validate passwords
            if (!$newPassword || strlen($newPassword) < 6) {
                $this->addFlash('error', 'Password must be at least 6 characters long.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }
            
            // Update password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $newPassword
                )
            );
            
            // Clear reset token
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            
            $entityManager->flush();
            
            // Log the password reset activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($user);
            $activityLog->setUsername($user->getUsername());
            $activityLog->setRole(json_encode($user->getRoles()));
            $activityLog->setAction('PASSWORD_RESET_COMPLETE');
            $activityLog->setTargetData('Password reset completed for: ' . $user->getEmail());
            $entityManager->persist($activityLog);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your password has been reset successfully. Please login with your new password.');
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('PasswordResetFolder/reset_password.html.twig', [
            'token' => $token
        ]);
    }
}
