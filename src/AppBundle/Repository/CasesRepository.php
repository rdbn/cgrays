<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesRepository extends EntityRepository
{
    /**
     * @param string $domainId
     * @return array
     */
    public function findCasesByCategoryId($domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT  
          DISTINCT c.id, c.name, c.image, c.price, c.created_at, c.cases_category_id as category_id
        FROM cases c
          LEFT JOIN cases_domain cd ON c.cases_domain_id = cd.id 
          LEFT JOIN cases_category cc ON c.cases_category_id = cc.id
          LEFT JOIN cases_skins cs ON c.id = cs.cases_id
        WHERE cd.uuid = :domain_id
        ');
        $stmt->bindParam('domain_id', $domainId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $domainId
     * @param $casesId
     * @return array
     */
    public function findCasesSkinsByDomainIdAndCasesId($domainId, $casesId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.casesDomain', 'cd')
            ->andWhere('c.id = :cases_id')
            ->andWhere('cd.uuid = :uuid')
            ->setParameter('cases_id', $casesId)
            ->setParameter('uuid', $domainId);

        return $qb->getQuery()->getOneOrNullResult();
    }
}