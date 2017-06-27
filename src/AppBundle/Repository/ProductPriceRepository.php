<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 02.05.17
 * Time: 20:52
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ProductPriceRepository extends EntityRepository
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
            ->leftJoin("pr.product", "p")
            ->where("pr.users = :user")
            ->setParameter("user", $user);

        try {
            return $qb->getQuery()->getResult();
        } catch (\Exception $e) {
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
            ->addSelect('p.class_id')
            ->addSelect('p.instance_id')
            ->from('product_price', 'pr')
            ->leftJoin('pr', 'product', 'p', 'pr.product_id = p.id')
            ->where('pr.user_id = :user_id');

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}