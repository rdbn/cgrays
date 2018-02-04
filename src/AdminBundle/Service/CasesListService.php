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
use Psr\Log\LoggerInterface;

class CasesListService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CasesListService constructor.
     * @param EntityManager $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param $casesId
     * @param $offset
     * @param $limit
     * @param array $filter
     * @return array
     */
    public function getList($casesId = null, $offset = 0, $limit = 18, array $filter = [])
    {
        if ($casesId) {
            $casesSkins = $this->em->getRepository(CasesSkins::class)
                ->findCasesSkinsByCasesId($casesId);

            $listCases = [];
            foreach ($casesSkins as $casesSkin) {
                $listCases[$casesSkin['skins_id']] = $casesSkin;
            }
        }
        $skins = $this->em->getRepository(Skins::class)
            ->findAllSkinsByFilter($filter, $offset, $limit);

        $listSkins = [];
        foreach ($skins as $index => $skin) {
            /* @var Skins $skin */
            $skinsId = $skin->getId();
            $listSkins[$index] = [
                'id' => $casesId,
                'skins_id' => $skinsId,
                'name' => mb_strimwidth($skin->getName(), 0, 25, '...', 'utf-8'),
                'icon_url' => $skin->getIconUrl(),
                'rarity_id' => $skin->getRarity()->getId(),
                'is_skins_case' => 0,
            ];

            if (isset($listCases)) {
                $listSkins[$index]['is_skins_case'] = isset($listCases[$skinsId]) ? 1 : 0;
            }
        }

        return [
            'list_skins' => $listSkins,
            'cases_skins' => isset($casesSkins) ? $casesSkins : [],
        ];
    }

    /**
     * @return int
     */
    public function getCountSkins()
    {
        try {
            $countSkins = $this->em->getRepository(Skins::class)
                ->getCountSkins();

            return $countSkins;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return 0;
    }
}
