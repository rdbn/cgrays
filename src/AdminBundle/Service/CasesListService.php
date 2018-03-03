<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.09.17
 * Time: 21:40
 */

namespace AdminBundle\Service;

use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
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
        $listCases = [];
        if ($casesId) {
            $casesSkins = $this->em->getRepository(CasesSkins::class)
                ->findCasesSkinsByCasesId($casesId);

            foreach ($casesSkins as $casesSkin) {
                $listCases[$casesSkin['skins_id']] = $casesSkin;
            }
        }

        try {
            $skins = $this->em->getRepository(Skins::class)
                ->findAllSkinsByFilter($filter, $offset, $limit);

            $skins = array_map(function ($item) use ($listCases) {
                $item['name'] = MbStrimWidthHelper::strimWidth($item['name']);
                $item['is_skins_case'] = 0;

                if (isset($listCases)) {
                    $item['is_skins_case'] = isset($listCases[$item['skins_id']]) ? 1 : 0;
                }

                return $item;
            }, $skins);

            return [
                'list_skins' => $skins,
                'cases_skins' => isset($casesSkins) ? $casesSkins : [],
            ];
        } catch (DBALException $e) {
            return [
                'list_skins' => [],
                'cases_skins' => [],
            ];
        }
    }

    /**
     * @param $casesId
     * @return array
     */
    public function getListRarity($casesId)
    {
        try {
            return $this->em->getRepository(Rarity::class)
                ->findRarityByCasesId($casesId);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
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
