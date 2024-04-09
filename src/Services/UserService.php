<?php

namespace App\Services;

use App\Entity\UserAccount;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserService
{
    public function GetUserWithTokenInterface(?TokenInterface $token = null): ?UserAccount
    {
        if ($token) {
            $user = $token->getUser();

            if (!($user instanceof UserAccount)) {
                $user = UserAccount::convertFrom($user);
            }

            return $user;
        } else {
            return null;
        }

    }
}