<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Currency;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCurrencyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var array
     */
    private $rubs = [
        "RUB" => 'руб.',
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->rubs as $code => $name) {
            $currency = new Currency();
            $currency->setName($name);
            $currency->setCode($code);

            $manager->persist($currency);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}