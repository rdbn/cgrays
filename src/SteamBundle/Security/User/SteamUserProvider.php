<?php

namespace SteamBundle\Security\User;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use SteamBundle\Service\SteamUserService;
use Doctrine\ORM\EntityManager;

class SteamUserProvider implements UserProviderInterface
{
    private $em;
    private $userClass;
    private $userService;

    public function __construct(EntityManager $em, SteamUserService $userService, $userClass)
    {
        $this->em = $em;
        $this->userClass = $userClass;
        $this->userService = $userService;
    }

    public function loadUserByUsername($steamId)
    {
        $userRepo = $this->em->getRepository($this->userClass);
        $user = $userRepo->findOneBy(['steamId' => $steamId]);

        if (!$user) {
            /* @var User $user */
            $user = new $this->userClass();
            $user->setSteamId($steamId);
            $user->setPassword(base64_encode(random_bytes(20)));
            $this->userService->updateUserEntry($user);

            $role = $this->em->getRepository(Role::class)
                ->findOneBy(["role" => "ROLE_USER"]);

            $user->addRole($role);

            $this->em->persist($user);
            $this->em->flush($user);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SteamUserInterface) {
            throw new UnsupportedUserException("User not supported");
        }
        return $this->loadUserByUsername($user->getSteamId());
    }

    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }
}
