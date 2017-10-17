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
        $casesCategories = $manager->getRepository(CasesCategory::class)
            ->findAll();

        $casesDomains = $manager->getRepository(CasesDomain::class)
            ->findAll();

        foreach ($casesCategories as $key => $casesCategory) {
            $skins = $manager->getRepository(Skins::class)
                ->findBy([], [], 5, 0);

            for ($s = 0; $s < 5; $s++) {
                $cases = new Cases();
                $cases->setName('Test' . uniqid());
                $cases->setCasesCategory($casesCategory);
                $cases->setCasesDomain($casesDomains[$key]);
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