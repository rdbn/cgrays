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
            $skinId = $casesSkin->getSkins()->getId();
            $rarityId = $casesSkin->getSkins()->getRarity()->getId();
            if (isset($sort[$rarityId])) {
                $casesSkin->setProcentRarity($sort[$rarityId]['rarity']);
                $casesSkin->setProcentSkins($sort[$rarityId]['skins'][$skinId]);
                unset($sort[$rarityId]);
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
        $skinsIds = [];
        foreach ($sort as $item) {
            if (isset($item['skins'])) {
                foreach (array_keys($item['skins']) as $key) {
                    $skinsIds[] = $key;
                }
            }
        }

        if (!count($skinsIds)) {
            return;
        }

        $skins = $om->getRepository(Skins::class)
            ->findSkinsByIds(implode(",", $skinsIds));

        foreach ($skins as $skin) {
            /* @var Skins $skin */
            $rarityId = $skin->getRarity()->getId();

            $casesSkins = new CasesSkins();
            $casesSkins->setSkins($skin);
            $casesSkins->setCases($cases);
            $casesSkins->setProcentRarity($sort[$rarityId]['rarity']);
            $casesSkins->setProcentSkins($sort[$rarityId]['skins'][$skin->getId()]);

            $om->persist($casesSkins);
        }
    }
}