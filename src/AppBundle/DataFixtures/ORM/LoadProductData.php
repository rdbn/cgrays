<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:17
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Heroes;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeProduct;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)
            ->findAll();

        for ($i = 0; $i < 1; $i++) {
            $heroes = $manager->getRepository(Heroes::class)
                ->findOneBy(["id" => rand(1, 5)]);

            $typeProduct = $manager->getRepository(TypeProduct::class)
                ->findOneBy(["id" => 1]);

            $quality = $manager->getRepository(Quality::class)
                ->findOneBy(["id" => rand(1, 5)]);

            $rareness = $manager->getRepository(Rarity::class)
                ->findOneBy(["id" => rand(1, 5)]);

            $product = new Product();
            $product->setClassId(rand($i * 100000, $i + 5 * 100000));
            $product->setInstanceId(rand($i * 100000, $i + 5 * 100000));
            $product->setHeroes($heroes);
            $product->setTypeProduct($typeProduct);
            $product->setQuality($quality);
            $product->setRarity($rareness);
            $product->setName("Product " . uniqid());
            $product->setIconUrl("300.png");
            $product->setIconUrlLarge("300.png");
            $product->setDescription("
                Зачарованная стамеска для создания гнезд под любые самоцветы, кроме потусторонних и призматических. 
                Этот инструмент можно применить на странице информации о предмете. Откройте арсенал и дважды нажмите 
                на нужный предмет, или же нажмите на него правой кнопкой мыши и выберите пункт «Информация». После использования 
                этот инструмент исчезнет.
            ");

            $manager->persist($product);

            for ($k = 0; $k < 5; $k++) {
                $productPrice = new ProductPrice();
                $productPrice->setProduct($product);
                $productPrice->setUsers($users[$k]);
                $productPrice->setPrice(1000 + rand(100, 500));

                $manager->persist($productPrice);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 30;
    }

}