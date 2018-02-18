<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 15.10.17
 * Time: 15:57
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\Skins;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class CasesContractHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Connection
     */
    private $dbal;

    /**
     * CasesContractHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     */
    public function __construct(EntityManager $em, Connection $dbal)
    {
        $this->em = $em;
        $this->dbal = $dbal;
    }

    /**
     * @param array $skinsPickUpUserIds
     * @param $domainId
     * @param $userId
     * @param $currencyId
     * @throws \Exception
     */
    public function handler(array $skinsPickUpUserIds, $domainId, $userId, $currencyId)
    {
        $casesDomain = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$casesDomain) {
            throw new \Exception('Not found domain');
        }

        $this->dbal->beginTransaction();
        try {
            $casesUserBalance = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain($userId, $currencyId, $casesDomain->getId());

            $casesSkinsPickUpUser = $this->em->getRepository(CasesSkinsPickUpUser::class)
                ->findSkinsForUpdateByIds($skinsPickUpUserIds);

            $sum = 0;
            foreach ($casesSkinsPickUpUser as $item) {
                $skin = $this->em->getRepository(Skins::class)
                    ->findSkinsForUpdateById($item['skins_id']);

                $sum += $skin['steam_price'];
                $this->dbal->delete('cases_skins_pick_up_user', ['id' => $item['id']]);
            }

            $this->dbal->update(
                'cases_balance_user',
                ['balance' => (float)$casesUserBalance['balance'] + (float) $sum],
                ['id' => $casesUserBalance['id']]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}