<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 19:30
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Skins;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

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
     * @var ProducerInterface
     */
    private $producer;

    /**
     * CasesUserSellSkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param ProducerInterface $producer
     */
    public function __construct(EntityManager $em, Connection $dbal, ProducerInterface $producer)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->producer = $producer;
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
            $skins = $this->em->getRepository(Skins::class)
                ->findSkinsForUpdateById($resultGame['skins_id']);

            $userBalance = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain(
                    $userId,
                    $currencyId,
                    $casesDomain->getId()
                );

            $balance = (int) $skins['steam_price'] + (int) $userBalance['balance'];
            $this->dbal->update('cases_balance_user', ['balance' => $balance], ['id' => $userBalance['id']]);

            $resultGame['skins_name'] = MbStrimWidthHelper::strimWidth($resultGame['skins_name']);
            unset($resultGame['skins_id'], $resultGame['cases_domain_id']);
            $this->producer->publish(json_encode($resultGame));

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $balance;
    }
}