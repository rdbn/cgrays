<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesCategoryRepository extends EntityRepository
{
    /**
     * @param $domainId
     * @return array
     */
    public function findAllCasesDomain($domainId)
    {
        $dbal =  $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT DISTINCT cc.id, cc.name FROM cases_category cc
          LEFT JOIN cases c ON c.cases_category_id = cc.id
          LEFT JOIN cases_domain cd ON cd.id = c.cases_domain_id
        WHERE cd.uuid = :domain_id
        ORDER BY cc.id
        ');
        $stmt->bindParam('domain_id', $domainId, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}