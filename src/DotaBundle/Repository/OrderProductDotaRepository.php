<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace DotaBundle\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class OrderProductDotaRepository extends EntityRepository
{
    public function findOrderProductsByUserId($id)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("o.id")
            ->addSelect("pr.product_id")
            ->addSelect("p.icon_url_large")
            ->addSelect("p.name")
            ->addSelect("pr.price")
            ->addSelect("o.product_price_dota_id as price_id")
            ->from("order_product_dota", "o")
            ->leftJoin("o", "product_price_dota", "pr", "o.product_price_dota_id = pr.id")
            ->leftJoin("pr", "product_dota", "p", "pr.product_dota_id = p.id")
            ->where("o.user_id = :id");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam("id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        try {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            return [];
        }
    }
}