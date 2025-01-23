<?php

namespace App\Security;

use App\Entity\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ClientVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $client = $subject;
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Autoriser si l'utilisateur est ADMIN ou MANAGER
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MANAGER')) {
            return true;
        }

        // Vérifier si l'utilisateur est bien propriétaire du client
        return $client->getUser() === $user;
        

    }
}
