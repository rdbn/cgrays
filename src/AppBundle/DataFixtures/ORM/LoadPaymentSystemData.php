<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\PaymentSystem;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPaymentSystemData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $paymentSystem = new PaymentSystem();
        $paymentSystem->setName('Яндекс деньги.');

        $manager->persist($paymentSystem);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}