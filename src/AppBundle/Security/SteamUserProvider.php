<?php

namespace AppBundle\Security;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use SteamBundle\Security\User\SteamUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use AppBundle\Services\SteamUserService;
use Doctrine\ORM\EntityManager;

class SteamUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SteamUserService
     */
    private $userService;

    /**
     * @var User
     */
    private $userClass;

    /**
     * SteamUserProvider constructor.
     * @param EntityManager $em
     * @param SteamUserService $userService
     * @param $userClass
     */
    public function __construct(EntityManager $em, SteamUserService $userService, $userClass)
    {
        $this->em = $em;
        $this->userClass = $userClass;
        $this->userService = $userService;
    }

    /**
     * @param string $steamId
     * @return User|null|object
     */
    public function loadUserByUsername($steamId)
    {
        $user = $this->em->getRepository($this->userClass)
            ->findUserByUsernameOrSteamId($steamId);

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

    /**
     * @param UserInterface $user
     * @return User|null|object
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SteamUserInterface) {
            throw new UnsupportedUserException("User not supported");
        }
        return $this->loadUserByUsername($user->getSteamId());
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }

    /**
     * @param $apiKey
     * @return mixed
     */
    public function getUsernameForApiKey($apiKey)
    {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $username = "admin";

        return $username;
    }
}
