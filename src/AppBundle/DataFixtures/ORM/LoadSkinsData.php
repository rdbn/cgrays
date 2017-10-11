<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:17
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\Quality;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\User;
use AppBundle\Entity\Weapon;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSkinsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)
            ->findAll();

        for ($i = 0; $i < 15; $i++) {
            $id = $i + 1;
            $typeProduct = $manager->getRepository(TypeSkins::class)
                ->findOneBy(["id" => $id]);

            $quality = $manager->getRepository(Quality::class)
                ->findOneBy(["id" => $id]);

            $rarity = $manager->getRepository(Rarity::class)
                ->findOneBy(["id" => $id]);

            $itemSet = $manager->getRepository(ItemSet::class)
                ->findOneBy(["id" => $id]);

            $decor = $manager->getRepository(Decor::class)
                ->findOneBy(["id" => $id]);

            $skins = new Skins();
            $skins->setTypeSkins($typeProduct);
            $skins->setQuality($quality);
            $skins->setRarity($rarity);
            $skins->setItemSet($itemSet);
            $skins->setDecor($decor);
            $skins->setName("Product " . uniqid());
            $skins->setIconUrl("image/300.png");
            $skins->setIconUrlLarge("image/300.png");
            $skins->setDescription("
                Зачарованная стамеска для создания гнезд под любые самоцветы, кроме потусторонних и призматических. 
                Этот инструмент можно применить на странице информации о предмете. Откройте арсенал и дважды нажмите 
                на нужный предмет, или же нажмите на него правой кнопкой мыши и выберите пункт «Информация». После использования 
                этот инструмент исчезнет.
            ");

            $manager->persist($skins);

            for ($k = 0; $k < 5; $k++) {
                $skinsPrice = new SkinsPrice();
                $skinsPrice->setClassId(rand($i * 100000, $i + 5 * 100000));
                $skinsPrice->setInstanceId(rand($i * 100000, $i + 5 * 100000));
                $skinsPrice->setAssetId(rand($i * 100000, $i + 5 * 100000));
                $skinsPrice->setSkins($skins);
                $skinsPrice->setUsers($users[$k]);
                $skinsPrice->setPrice(1000 + rand(100, 500));

                $manager->persist($skinsPrice);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }

}