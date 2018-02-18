<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\BalanceUser;
use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBalanceData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $balanceUsers = $manager->getRepository(BalanceUser::class)
            ->findAll();

        foreach ($balanceUsers as $balanceUser) {
            $balanceUser->setBalance(1000);
        }

        $casesBalanceUsers = $manager->getRepository(CasesBalanceUser::class)
            ->findAll();

        foreach ($casesBalanceUsers as $casesBalanceUser) {
            $casesBalanceUser->setBalance(1000);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}