<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.07.17
 * Time: 10:24
 */

namespace AppBundle\Services\Payment;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
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
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AddBalanceService constructor.
     * @param Connection $dbal
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->userRepository = $userRepository;
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
            $paymentSystemId = $form->get('paymentSystem')->getData()->getId();
            $sumPayment = (int)$form->get('sum_payment')->getData();

            $this->dbal->beginTransaction();
            try {
                $user = $this->userRepository->findUserForUpdateById($userId);

                $balance = $user['balance'];
                if ($actionPay == 'in') {
                    $balance += $sumPayment;
                } else {
                    $balance -= $sumPayment;
                }

                $this->dbal->update('users', ['balance' => $balance], ['id' => $userId]);
                $this->dbal->insert('payment', [
                    'payment_system_id' => $paymentSystemId,
                    'user_id' => $userId,
                    'payment_information' => "Information {$actionPay}: {$sumPayment}",
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