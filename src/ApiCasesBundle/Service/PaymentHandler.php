<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 05.02.2018
 * Time: 11:15
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class PaymentHandler
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PaymentHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, Connection $dbal, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->logger = $logger;
    }

    /**
     * @param $domainId
     * @param $steamId
     * @param $paymentInformation
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function handle($domainId, $steamId, $paymentInformation)
    {
        $domainId = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$domainId) {
            throw new \Exception("Not found domain by domain id {$domainId}.");
        }

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['steamId' => $steamId]);

        if (!$user) {
            throw new \Exception("Not found user by steam id {$steamId}.");
        }

        $currency = $this->em->getRepository(Currency::class)
            ->findOneBy(['code' => 'RUB']);

        if (!$currency) {
            throw new \Exception("Not found currency by RUB.");
        }

        $date = new \DateTime();

        $this->dbal->beginTransaction();
        try {
            $casesBalanceUser = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain($user->getId(), 1, $domainId->getId());

            $balance = $casesBalanceUser['balance'] + $paymentInformation['withdraw_amount'];
            $this->dbal->update('cases_balance_user', ['balance' => $balance], ['id' => $casesBalanceUser['id']]);
            $this->dbal->insert('payment', [
                'user_id' => $user->getId(),
                'currency_id' => $currency->getId(),
                'payment_information' => json_encode($paymentInformation),
                'created_at' => $date->format('Y-m-d H:i:s'),
            ]);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}