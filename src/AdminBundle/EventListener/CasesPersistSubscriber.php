<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 01.10.17
 * Time: 18:57
 */

namespace AdminBundle\EventListener;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\Skins;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CasesPersistSubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /* @var Cases $cases */
        $cases = $args->getObject();
        if (!$cases instanceof Cases) {
            return;
        }

        $sort = json_decode($cases->getSort(), 1);

        $om = $args->getObjectManager();
        $this->addCasesSkins($om, $cases, $sort);
        $om->flush();
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        /* @var Cases $cases */
        $cases = $args->getObject();
        if (!$cases instanceof Cases) {
            return;
        }

        $sort = json_decode($cases->getSort(), 1);

        $om = $args->getObjectManager();
        $casesSkins = $cases->getCasesSkins();
        foreach ($casesSkins as $casesSkin) {
            /* @var CasesSkins $casesSkin */
            $id = $casesSkin->getSkins()->getId();
            if (isset($sort[$id])) {
                $casesSkin->setCount($sort[$id]);
                unset($sort[$id]);
            } else {
                $om->remove($casesSkin);
            }
        }

        if (count($sort) > 0) {
            $this->addCasesSkins($om, $cases, $sort);
        }

        $om->flush();
    }

    /**
     * @param ObjectManager $om
     * @param Cases $cases
     * @param $sort
     */
    private function addCasesSkins(ObjectManager $om, Cases $cases, $sort)
    {
        $skins = $om->getRepository(Skins::class)
            ->findSkinsByIds(implode(",", array_keys($sort)));

        foreach ($skins as $skin) {
            /* @var Skins $skin */
            $casesSkins = new CasesSkins();
            $casesSkins->setSkins($skin);
            $casesSkins->setCases($cases);
            $casesSkins->setCount($sort[$skin->getId()]);

            $om->persist($casesSkins);
        }
    }
}