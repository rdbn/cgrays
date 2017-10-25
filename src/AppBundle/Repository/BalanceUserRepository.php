<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BalanceUserRepository extends EntityRepository
{
    /**
     * @param $userId
     * @param $currencyId
     * @return mixed
     */
    public function findBalanceUserForUpdateByUserIdCurrencyId($userId, $currencyId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT bu.* FROM balance_user bu WHERE bu.user_id = :user_id AND bu.currency_id = :currency_id FOR UPDATE
        ");
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam('currency_id', $currencyId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}