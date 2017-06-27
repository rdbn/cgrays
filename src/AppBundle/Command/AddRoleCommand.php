<?php

namespace AppBundle\Command;

use AppBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRoleCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    private $roles = [
        "ROLE_USER",
        "ROLE_ADMIN",
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:add_role')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");

        foreach ($this->roles as $name) {
            $role = new Role();
            $role->setRole($name);

            $em->persist($role);
        }

        $em->flush();
    }
}
