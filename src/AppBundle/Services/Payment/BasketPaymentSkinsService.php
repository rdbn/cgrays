<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 09.08.17
 * Time: 10:26
 */

namespace AppBundle\Services\Payment;

use AppBundle\Entity\SkinsTrade;
use AppBundle\Repository\BalanceUserRepository;
use AppBundle\Repository\SkinsPriceRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

class BasketPaymentSkinsService
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
     * @var SkinsPriceRepository
     */
    private $priceRepository;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PaymentProductService constructor.
     * @param Connection $dbal
     * @param BalanceUserRepository $balanceUserRepository
     * @param SkinsPriceRepository $priceRepository
     * @param ProducerInterface $producer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Connection $dbal,
        BalanceUserRepository $balanceUserRepository,
        SkinsPriceRepository $priceRepository,
        ProducerInterface $producer,
        LoggerInterface $logger
    )
    {
        $this->dbal = $dbal;
        $this->balanceUserRepository = $balanceUserRepository;
        $this->priceRepository = $priceRepository;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param int $currencyId
     * @param int $skinsPriceIds
     * @param int $userId
     * @throws \Exception
     */
    public function handler($currencyId, $skinsPriceIds, $userId)
    {
        $this->dbal->beginTransaction();
        try {
            $skinsPrices = $this->priceRepository->findSkinsPriceForUpdateBySkinsPriceIds($skinsPriceIds);
            $sum = $this->priceRepository->findSumSkinsPriceBySkinsPriceIds($skinsPriceIds);
            $user = $this->balanceUserRepository->findBalanceUserForUpdateByUserIdCurrencyId($userId, $currencyId);

            $balance = (int) $user['balance'] - (int) $sum;
            if ((int) $balance < 0) {
                throw new \Exception("Balance {$balance} user - {$userId} < skins price: {$skinsPriceIds}", '403');
            }

            $this->updateSkinsTradeIds($skinsPriceIds, $userId);
            $this->updateBalanceUser($userId, $currencyId, $balance);
            $this->updateSkinsPriceIds($skinsPriceIds, $userId);
            $this->insertPayment($userId, $currencyId, $balance);
            $this->sendSkinsTrade($skinsPrices);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception("userId: {$userId}, skinsPriceId: {$skinsPriceIds}. " . $e->getMessage());
        }
    }

    /**
     * @param $skinsPriceIds
     * @param $userId
     */
    private function updateSkinsTradeIds($skinsPriceIds, $userId)
    {
        $stmt = $this->dbal->prepare("
        UPDATE skins_trade SET status = :status WHERE user_id = :user_id AND skins_price_id IN ({$skinsPriceIds})
        ");
        $stmt->bindValue('status', SkinsTrade::SKINS_BEGIN_TRADE, \PDO::PARAM_STR);
        $stmt->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @param $skinsPriceIds
     * @param $userId
     */
    private function updateSkinsPriceIds($skinsPriceIds, $userId)
    {
        $stmt = $this->dbal->prepare("
        UPDATE skins_price SET is_remove = TRUE WHERE user_id = :user_id AND id IN ({$skinsPriceIds})
        ");
        $stmt->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @param $userId
     * @param $balance
     * @param $currencyId
     */
    private function updateBalanceUser($userId, $currencyId, $balance)
    {
        $this->dbal->update(
            'balance_user',
            ['balance' => $balance],
            ['user_id' => $userId, 'currency_id' => $currencyId]
        );
    }

    /**
     * @param $userId
     * @param $currencyId
     * @param $balance
     */
    private function insertPayment($userId, $currencyId, $balance)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $this->dbal->insert('payment', [
            'user_id' => $userId,
            'currency_id' => $currencyId,
            'payment_information' => $balance,
            'created_at' => $date,
        ]);
    }

    private function sendSkinsTrade($skinsPrices)
    {
        foreach ($skinsPrices as $skinsPrice) {
            $this->producer->publish(json_encode([
                'id' => $skinsPrice['id'],
                'class_id' => $skinsPrice['class_id'],
                'instance_id' => $skinsPrice['instance_id'],
            ]));
        }
    }
}