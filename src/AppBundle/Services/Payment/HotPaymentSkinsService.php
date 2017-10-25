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

class HotPaymentSkinsService
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
     * @param int $skinsPriceId
     * @param int $userId
     * @throws \Exception
     */
    public function handler($currencyId, $skinsPriceId, $userId)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $this->dbal->beginTransaction();
        try {
            $skinsPrice = $this->priceRepository->findSkinsPriceForUpdateBySkinsPriceId($skinsPriceId);
            $user = $this->balanceUserRepository->findBalanceUserForUpdateByUserIdCurrencyId($userId, $currencyId);

            $balance = (int) $user['balance'] - $skinsPrice['price'];
            if ((int) $balance < 0) {
                throw new \Exception("Balance user - {$userId} < skins price: {$skinsPriceId}", '403');
            }

            $this->dbal->update(
                'balance_user',
                ['balance' => $balance],
                ['user_id' => $userId, 'currency_id' => $currencyId]
            );

            $this->dbal->update(
                'skins_price',
                ['is_remove' => true],
                ['id' => $skinsPriceId]
            );

            $this->dbal->insert('skins_trade', [
                'user_id' => $userId,
                'skins_price_id' => $skinsPriceId,
                'status' => SkinsTrade::SKINS_BEGIN_TRADE,
                'created_at' => $date,
            ]);

            $this->dbal->insert('payment', [
                'user_id' => $userId,
                'currency_id' => $currencyId,
                'payment_information' => $balance,
                'created_at' => $date,
            ]);

            $this->producer->publish(json_encode([
                'id' => $skinsPrice['id'],
                'class_id' => $skinsPrice['class_id'],
                'instance_id' => $skinsPrice['instance_id'],
            ]));

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception("userId: {$userId}, skinsPriceId: {$skinsPriceId}. " . $e->getMessage());
        }
    }
}