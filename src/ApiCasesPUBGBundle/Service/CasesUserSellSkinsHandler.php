<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 19:30
 */

namespace ApiCasesPUBGBundle\Service;

use ApiCasesBundle\Service\MetricsEventSender;
use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\SkinsPUBG;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class CasesUserSellSkinsHandler
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
     * @var MetricsEventSender
     */
    private $eventSender;

    /**
     * CasesUserSellSkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param MetricsEventSender $eventSender
     */
    public function __construct(EntityManager $em, Connection $dbal, MetricsEventSender $eventSender)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->eventSender = $eventSender;
    }

    /**
     * @param array $resultGame
     * @param int $userId
     * @param $domainId
     * @param $currencyId
     * @throws \Exception
     * @return int
     */
    public function handler(array $resultGame, int $userId, $domainId, $currencyId)
    {
        $casesDomain = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$casesDomain) {
            throw new \Exception('Not found domain');
        }

        $this->dbal->beginTransaction();
        try {
            $skins = $this->em->getRepository(SkinsPUBG::class)
                ->findOneBy(['id' => $resultGame['skins_id']]);

            $userBalance = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain(
                    $userId,
                    $currencyId,
                    $casesDomain->getId()
                );

            $balance = (float) $skins->getSteamPrice() + (float) $userBalance['balance'];
            $this->dbal->update('cases_balance_user', ['balance' => $balance], ['id' => $userBalance['id']]);

            $this->eventSender->sender([
                'user_id' => $userId,
                'cases_id' => $resultGame['cases_id'],
                'cases_domain_id' => $resultGame['cases_domain_id'],
                'cases_category_id' => $resultGame['cases_category_id'],
                'price' => (float) $skins->getSteamPrice(),
                'event_type' => 'sell_skins',
            ]);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $balance;
    }
}