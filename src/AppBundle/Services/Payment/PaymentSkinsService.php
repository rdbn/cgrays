<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 09.08.17
 * Time: 10:26
 */

namespace AppBundle\Services\Payment;

use AppBundle\Entity\SkinsTrade;
use AppBundle\Repository\SkinsPriceRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

class PaymentSkinsService
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
     * @param UserRepository $userRepository
     * @param SkinsPriceRepository $priceRepository
     * @param ProducerInterface $producer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Connection $dbal,
        UserRepository $userRepository,
        SkinsPriceRepository $priceRepository,
        ProducerInterface $producer,
        LoggerInterface $logger
    )
    {
        $this->dbal = $dbal;
        $this->userRepository = $userRepository;
        $this->priceRepository = $priceRepository;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param int $paymentSystemId
     * @param int $skinsPriceId
     * @param int $userId
     * @param boolean $isFastTrade
     */
    public function handler($paymentSystemId, $skinsPriceId, $userId, $isFastTrade = false)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $this->dbal->beginTransaction();
        try {
            $skinsPrice = $this->priceRepository->findSkinsPriceForUpdateBySkinsPriceId($skinsPriceId);
            $user = $this->userRepository->findUserForUpdateById($userId);

            $balance = (int) $user['balance'] - $skinsPrice['price'];
            $this->dbal->update('users', ['balance' => $balance], ['id' => $userId]);
            $this->dbal->update('skins_price', ['is_remove' => true], ['id' => $skinsPriceId]);
            $this->dbal->insert('payment', [
                'user_id' => $userId,
                'payment_system_id' => $paymentSystemId,
                'payment_information' => $balance,
                'created_at' => $date,
            ]);

            if ($isFastTrade) {
                $this->dbal->insert('skins_trade', [
                    'user_id' => $userId,
                    'skins_price_id' => $skinsPriceId,
                    'status' => SkinsTrade::SKINS_BEGIN_TRADE,
                    'created_at' => $date,
                ]);
            }

            $this->producer->publish(json_encode([
                'id' => $skinsPrice['id'],
                'class_id' => $skinsPrice['class_id'],
                'instance_id' => $skinsPrice['instance_id'],
            ]));

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error("userId: {$userId}, skinsPriceId: {$skinsPriceId}. " . $e->getMessage());
        }
    }
}