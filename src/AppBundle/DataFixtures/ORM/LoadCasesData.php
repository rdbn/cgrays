<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\Skins;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCasesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $casesDomain = new CasesDomain();
            $casesDomain->setDomain('http://test' . uniqid() . '.ru');

            $manager->persist($casesDomain);

            $casesCategory = new CasesCategory();
            $casesCategory->setName('Test' . uniqid());

            $manager->persist($casesCategory);

            $skins = $manager->getRepository(Skins::class)
                ->findBy([], [], 5, 0);

            for ($s = 0; $s < 5; $s++) {
                $cases = new Cases();
                $cases->setName('Test' . uniqid());
                $cases->setCasesCategory($casesCategory);
                $cases->setCasesDomain($casesDomain);
                $cases->setPrice(rand(5, 20));
                $cases->setImage('image/300.png');

                $sort = [];
                foreach ($skins as $skin) {
                    $count = rand(10, 15);
                    $sort[$skin->getId()] = $count;
                }
                $cases->setSort(json_encode($sort));

                $manager->persist($cases);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}