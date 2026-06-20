<?php

namespace App\Security\Voter;

use App\Database\Entity\Application;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ApplicationVoter extends Voter
{
    public const string VIEW = 'APPLICATION_VIEW';

    /**
     * @param string $attribute
     * @param $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Application;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) return false;

        /** @var Application $application */
        $application = $subject;
        $ownerUser = $application->student ?? $application->student?->getUser();

        if (!$ownerUser) {
            return false;
        }

        return (string)$ownerUser->getId() === (string)$user->getId();
    }

}

