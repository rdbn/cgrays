<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDictionarySkinsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            $name = uniqid();
            $typeProduct = new TypeSkins();
            $typeProduct->setInternalName($name);
            $typeProduct->setLocalizedTagName($name);
            $manager->persist($typeProduct);

            $quality = new Quality();
            $quality->setInternalName($name);
            $quality->setLocalizedTagName($name);
            $manager->persist($quality);

            $rarity = new Rarity();
            $rarity->setInternalName($name);
            $rarity->setLocalizedTagName($name);
            $rarity->setColor('4b69ff');
            $manager->persist($rarity);

            $itemSet = new ItemSet();
            $itemSet->setInternalName($name);
            $itemSet->setLocalizedTagName($name);
            $manager->persist($itemSet);

            $weapon = new Weapon();
            $weapon->setInternalName($name);
            $weapon->setLocalizedTagName($name);
            $manager->persist($weapon);

            $decor = new Decor();
            $decor->setInternalName($name);
            $decor->setLocalizedTagName($name);
            $manager->persist($decor);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}