<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class SkinsTradeRepository extends EntityRepository
{
    public function findOrderSkinsByUserId($id)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("so.id")
            ->addSelect("sp.skins_id")
            ->addSelect("s.icon_url")
            ->addSelect("s.name")
            ->addSelect("sp.price")
            ->addSelect("so.skins_price_id as price_id")
            ->from("skins_trade", "so")
            ->leftJoin("so", "skins_price", "sp", "so.skins_price_id = sp.id")
            ->leftJoin("sp", "skins", "s", "sp.skins_id = s.id")
            ->where("so.user_id = :id");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam("id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        try {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            return [];
        }
    }

    public function findSumAllOrderSkinsByUserId($id)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("SUM(sp.price) as sum_all")
            ->from("skins_trade", "so")
            ->leftJoin("so", "skins_price", "sp", "so.skins_price_id = sp.id")
            ->leftJoin("sp", "skins", "s", "sp.skins_id = s.id")
            ->where("so.user_id = :id");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam("id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        try {
            return $stmt->fetchColumn(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * @param $userId
     * @return int
     */
    public function findCountSkinsTradeByUserId($userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('SELECT count(st.id) as count_skins FROM skins_trade st WHERE st.user_id = :id');
        $stmt->bindParam('id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}