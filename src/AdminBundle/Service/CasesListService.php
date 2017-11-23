<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.09.17
 * Time: 21:40
 */

namespace AdminBundle\Service;

use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\Skins;
use Doctrine\ORM\EntityManager;

class CasesListService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CasesListService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $casesId
     * @return array
     */
    public function getList($casesId = null)
    {
        if ($casesId) {
            $listCases = $this->getListCases($casesId);
        }
        $skins = $this->em->getRepository(Skins::class)
            ->findBy([], [], 42, 0);

        $listSkins = [];
        foreach ($skins as $index => $skin) {
            /* @var Skins $skin */
            $skinsId = $skin->getId();
            $listSkins[$index] = [
                'id' => $casesId,
                'skins_id' => $skinsId,
                'name' => $skin->getName(),
                'icon_url' => $skin->getIconUrl(),
                'count' => 0,
                'count_drop' => 0,
                'is_skins_case' => 0,
            ];

            if (isset($listCases)) {
                $listSkins[$index]['count'] = isset($listCases[$skinsId]) ? $listCases[$skinsId]['count']: 0;
                $listSkins[$index]['count_drop'] = isset($listCases[$skinsId]) ? $listCases[$skinsId]['count_drop']: 0;
                $listSkins[$index]['is_skins_case'] = isset($listCases[$skinsId]) ? 1 : 0;
            }
        }

        return $listSkins;
    }

    /**
     * @param $casesId
     * @return array
     */
    private function getListCases($casesId)
    {
        $casesSkins = $this->em->getRepository(CasesSkins::class)
            ->findCasesSkinsByCasesId($casesId);

        $listCases = [];
        foreach ($casesSkins as $casesSkin) {
            $listCases[$casesSkin['skins_id']] = $casesSkin;
        }

        return $listCases;
    }
}
