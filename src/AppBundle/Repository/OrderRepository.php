<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
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
            ->addSelect("o.product_price_id as price_id")
            ->from("order_product", "o")
            ->leftJoin("o", "product_price", "pr", "o.product_price_id = pr.id")
            ->leftJoin("pr", "product", "p", "pr.product_id = p.id")
            ->where("o.user_id = :id");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam("id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        try {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}