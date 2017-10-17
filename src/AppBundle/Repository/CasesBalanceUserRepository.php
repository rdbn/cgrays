<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesBalanceUserRepository extends EntityRepository
{
    /**
     * @param $userId
     * @param $currencyId
     * @param $casesDomainId
     * @return mixed
     */
    public function findUserBalanceForUpdateByUserIdCurrencyIdDomain($userId, $currencyId, $casesDomainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT 
          cbu.* 
        FROM cases_balance_user cbu
        WHERE 
          cbu.user_id = :user_id AND 
          cbu.currency_id = :currency_id AND 
          cbu.cases_domain_id = :cases_domain_id
        FOR UPDATE
        ");
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam('currency_id', $currencyId, \PDO::PARAM_INT);
        $stmt->bindParam('cases_domain_id', $casesDomainId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}