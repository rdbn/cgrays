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

        $om = $args->getObjectManager();
        $sort = $this->getNormalizeArray($cases);
        if (empty($sort['rarity_ids']) || empty($sort['skins_ids'])) {
            $om->flush();
            return;
        }

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

        $om = $args->getObjectManager();
        $sort = $this->getNormalizeArray($cases);
        if (empty($sort['rarity_ids']) || empty($sort['skins_ids'])) {
            $om->flush();

            return;
        }

        $casesSkins = $cases->getCasesSkins();
        foreach ($casesSkins as $casesSkin) {
            /* @var CasesSkins $casesSkin */
            $skinId = $casesSkin->getSkins()->getId();
            $rarityId = $casesSkin->getSkins()->getRarity()->getId();

            if (isset($sort['skins_ids'][$skinId])) {
                $casesSkin->setProcentRarity($sort['rarity_ids'][$rarityId]);
                $casesSkin->setProcentSkins($sort['skins_ids'][$skinId]);
                unset($sort['skins_ids'][$skinId]);
            } else {
                $om->remove($casesSkin);
            }
        }

        if (count($sort['skins_ids']) > 0) {
            $this->addCasesSkins($om, $cases, $sort);
        }

        $om->flush();
    }

    /**
     * @param Cases $cases
     * @return array
     */
    private function getNormalizeArray(Cases $cases)
    {
        $skinsIds = [];
        $rarityIds = [];
        $sort = json_decode($cases->getSort(), 1);
        if (!count($sort)) {
            return [
                'skins_ids' => [],
                'rarity_ids' => [],
            ];
        }

        foreach ($sort as $rarityId => $item) {
            $rarityIds[$rarityId] = $item['rarity'];

            if (isset($item['skins']) && !empty($item['skins']) && $item['rarity'] > 0) {
                foreach ($item['skins'] as $skinId => $skinProcent) {
                    $skinsIds[$skinId] = $skinProcent;
                }
            } else {
                unset($sort[$rarityId]);
            }
        }

        if (!count($sort)) {
            $cases->setSort('');
        } else {
            $cases->setSort(json_encode($sort));
        }

        return [
            'skins_ids' => $skinsIds,
            'rarity_ids' => $rarityIds,
        ];
    }

    /**
     * @param ObjectManager $om
     * @param Cases $cases
     * @param $sort
     */
    private function addCasesSkins(ObjectManager $om, Cases $cases, $sort)
    {
        $skinsIds = array_keys($sort['skins_ids']);
        $skins = $om->getRepository(Skins::class)
            ->findSkinsByIds(implode(",", $skinsIds));

        foreach ($skins as $skin) {
            /* @var Skins $skin */
            $rarityId = $skin->getRarity()->getId();

            $casesSkins = new CasesSkins();
            $casesSkins->setSkins($skin);
            $casesSkins->setCases($cases);
            $casesSkins->setProcentRarity($sort['rarity_ids'][$rarityId]);
            $casesSkins->setProcentSkins($sort['skins_ids'][$skin->getId()]);

            $om->persist($casesSkins);
        }
    }
}