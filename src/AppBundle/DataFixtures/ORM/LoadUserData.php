<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface
{
    /**
     * @var array
     */
    private $users = [
        [
            "username" => "admin",
            "role" => "ROLE_ADMIN",
        ],
        [
            "username" => "user1",
            "role" => "ROLE_USER",
        ],
        [
            "username" => "user2",
            "role" => "ROLE_USER",
        ],
        [
            "username" => "user3",
            "role" => "ROLE_USER",
        ],
        [
            "username" => "user4",
            "role" => "ROLE_USER",
        ],
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userEntity = new User();
        $userEntity->setSteamId('76561198227914060');
        $userEntity->setUsername('rdbnko');
        $userEntity->setIsSell(true);

        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($userEntity, 'rdbnko');
        $userEntity->setPassword($password);

        $role = $manager->getRepository(Role::class)
            ->findOneBy(["role" => 'ROLE_USER']);

        $userEntity->addRole($role);

        $manager->persist($userEntity);

        foreach ($this->users as $user) {
            $userEntity = new User();
            $userEntity->setSteamId(rand(10000, 20000));
            $userEntity->setUsername($user["username"]);
            $userEntity->setIsSell(true);

            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($userEntity, $user["username"]);
            $userEntity->setPassword($password);

            $role = $manager->getRepository(Role::class)
                ->findOneBy(["role" => $user["role"]]);

            $userEntity->addRole($role);

            $manager->persist($userEntity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}