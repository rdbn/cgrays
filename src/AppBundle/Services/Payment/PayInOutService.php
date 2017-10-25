<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.07.17
 * Time: 10:24
 */

namespace AppBundle\Services\Payment;

use AppBundle\Entity\User;
use AppBundle\Repository\BalanceUserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;

class PayInOutService
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var BalanceUserRepository
     */
    private $balanceUserRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddBalanceService constructor.
     * @param Connection $dbal
     * @param BalanceUserRepository $balanceUserRepository
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, BalanceUserRepository $balanceUserRepository, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->balanceUserRepository = $balanceUserRepository;
        $this->logger = $logger;
    }

    /**
     * @param Form $form
     * @param User $user
     * @param string $actionPay
     * @return boolean
     */
    public function handler(Form $form, User $user, $actionPay)
    {
        if ($form->isValid()) {
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');

            $userId = $user->getId();
            $paymentSystemName = $form->get('paymentSystem')->getData()->getName();
            $currencyId = $form->get('currency')->getData()->getId();
            $sumPayment = (int)$form->get('sum_payment')->getData();

            $this->dbal->beginTransaction();
            try {
                $user = $this->balanceUserRepository
                    ->findBalanceUserForUpdateByUserIdCurrencyId($userId, $currencyId);

                $balance = $user['balance'];
                if ($actionPay == 'in') {
                    $balance += $sumPayment;
                } else {
                    $balance -= $sumPayment;
                }

                $this->dbal->update(
                    'balance_user',
                    ['balance' => $balance],
                    ['currency_id' => $currencyId, 'user_id' => $userId]
                );

                $this->dbal->insert('payment', [
                    'user_id' => $userId,
                    'payment_information' => "Information {$paymentSystemName} {$actionPay}: {$sumPayment}",
                    'created_at' => $date,
                ]);

                $this->dbal->commit();
            } catch (DBALException $e) {
                $this->dbal->rollback();
                $this->logger->error($e->getMessage());
                return false;
            }

            return true;
        }

        return false;
    }
}