<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesSkinsDropUser;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCasesSkinsDropData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)
            ->findAll();

        $casesSkins = $manager->getRepository(CasesSkins::class)
            ->findBy([], [], 10, 0);

        foreach ($users as $user) {
            foreach ($casesSkins as $casesSkin) {
                /* @var CasesSkins $casesSkin */
                $casesSkinsDropUser = new CasesSkinsDropUser();
                $casesSkinsDropUser->setUser($user);
                $casesSkinsDropUser->setSkins($casesSkin->getSkins());
                $casesSkinsDropUser->setCasesDomain($casesSkin->getCases()->getCasesDomain());

                $manager->persist($casesSkinsDropUser);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}