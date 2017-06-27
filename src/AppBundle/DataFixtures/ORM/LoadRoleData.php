<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoleData extends AbstractFixture
{
    /**
     * @var array
     */
    private $roles = [
        "ROLE_USER",
        "ROLE_ADMIN",
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->roles as $role) {
            $roleEntity = new Role();
            $roleEntity->setRole($role);

            $manager->persist($roleEntity);
        }

        $manager->flush();
    }
}