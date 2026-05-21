<?php

namespace App\EventSubscriber;

use App\Entity\ActivityLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        
        if (!$user instanceof User) {
            return;
        }

        $user->setIsOnline(true);
        $this->entityManager->persist($user);

        // Log the login activity
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setUsername($user->getUserIdentifier());
        $activityLog->setRole(json_encode($user->getRoles()));
        $activityLog->setAction('LOGIN');
        $activityLog->setTargetData('User logged in successfully');
        
        $this->entityManager->persist($activityLog);
        $this->entityManager->flush();
    }

    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        
        if (!$token) {
            return;
        }

        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return;
        }

        $user->setIsOnline(false);
        $this->entityManager->persist($user);

        // Log the logout activity
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setUsername($user->getUserIdentifier());
        $activityLog->setRole(json_encode($user->getRoles()));
        $activityLog->setAction('LOGOUT');
        $activityLog->setTargetData('User logged out successfully');
        
        $this->entityManager->persist($activityLog);
        $this->entityManager->flush();
    }
}
