<?php

namespace AppBundle\Command;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterAdminCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:register_admin')
            ->setDescription('Add first user admin');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get("doctrine.orm.entity_manager");

        $user = new User();
        $user
            ->setUsername("admin")
            ->setSteamId("123")
            ->setAvatar("image/300.png");

        $encoder = $container->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, "admin"));

        $role = $em->getRepository(Role::class)
            ->findOneBy(["role" => "ROLE_ADMIN"]);
        $user->addRole($role);

        $role = $em->getRepository(Role::class)
            ->findOneBy(["role" => "ROLE_USER"]);

        $user->addRole($role);

        $em->persist($user);
        $em->flush();
    }
}
