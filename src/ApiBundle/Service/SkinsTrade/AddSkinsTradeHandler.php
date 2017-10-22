<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 20.10.17
 * Time: 10:21
 */

namespace ApiBundle\Service\SkinsTrade;

use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\SkinsTrade;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class AddSkinsTradeHandler
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
     * AddSkinsTradeHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     */
    public function __construct(EntityManager $em, Connection $dbal)
    {
        $this->em = $em;
        $this->dbal = $dbal;
    }

    /**
     * @param $skinsPriceId
     * @param $userId
     * @throws \Exception
     * @return int
     */
    public function handler($skinsPriceId, $userId)
    {
        $date = new \DateTime();
        $this->dbal->beginTransaction();
        try {
            $skinsPrice = $this->em->getRepository(SkinsPrice::class)
                ->findSkinsPriceForUpdateBySkinsPriceId($skinsPriceId);

            $this->dbal->insert('skins_trade', [
                'skins_price_id' => $skinsPrice['id'],
                'user_id' => $userId,
                'status' => SkinsTrade::SKINS_BUY,
                'created_at' => $date->format('Y-m-d H:i:s'),
            ]);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $this->em->getRepository(SkinsTrade::class)
            ->findCountSkinsTradeByUserId($userId);
    }
}