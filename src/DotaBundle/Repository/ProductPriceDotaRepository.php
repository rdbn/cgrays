<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 02.05.17
 * Time: 20:52
 */

namespace DotaBundle\Repository;

use DotaBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ProductPriceDotaRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return array
     */
    public function findProductsByUser(User $user)
    {
        $qb = $this->createQueryBuilder("pr");
        $qb
            ->addSelect("p")
            ->leftJoin("pr.productDota", "p")
            ->where("pr.users = :user")
            ->andWhere("pr.isSell = true")
            ->andWhere("pr.isRemove = false")
            ->setParameter("user", $user);

        try {
            return $qb->getQuery()->getResult();
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * @param $userId
     * @return array
     */
    public function findProductsByUserArrayResult($userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();
        $qb
            ->addSelect('pr.class_id')
            ->addSelect('pr.instance_id')
            ->from('product_price_dota', 'pr')
            ->leftJoin('pr', 'product_dota', 'p', 'pr.product_dota_id = p.id')
            ->where('pr.user_id = :user_id')
            ->andWhere("pr.is_sell = TRUE")
            ->andWhere("pr.is_remove = FALSE");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $productPriceId
     * @return mixed
     */
    public function findProductForUpdateByProductPriceId($productPriceId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT pr.class_id, pr.instance_id, pr.id, pr.price FROM product_price_dota pr WHERE pr.id = :id FOR UPDATE
        ');
        $stmt->bindParam('id', $productPriceId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}