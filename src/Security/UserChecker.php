<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Check if account is deactivated
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('Your account has been deactivated. Please contact an administrator for assistance.');
        }

        // Block login if email is not verified
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('email_not_verified::' . $user->getEmail());
        }
    }

    public function checkPostAuth(UserInterface $user, ?\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token = null): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Additional checks after authentication can go here
    }
}
