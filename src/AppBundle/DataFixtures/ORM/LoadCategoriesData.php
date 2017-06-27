<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Heroes;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rareness;
use AppBundle\Entity\TypeProduct;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoriesData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $name = "tydpe product name" . uniqid();

        $category = new TypeProduct();
        $category->setName($name);
        $category->setTitle($name);

        $manager->persist($category);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}