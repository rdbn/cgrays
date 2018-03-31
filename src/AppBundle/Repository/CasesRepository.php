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
          DISTINCT c.id, c.name, c.image, c.price, c.promotion_price, c.created_at, c.cases_category_id as category_id
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findCasesSkinsByDomainIdAndCasesId($domainId, $casesId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT 
          c.id, c.name, c.price, c.image, c.created_at, c.cases_domain_id, c.cases_category_id
        FROM cases c
          LEFT JOIN cases_domain cd ON cd.id = c.cases_domain_id
        WHERE
          c.id = :id AND cd.uuid = :uuid
        ');
        $stmt->bindParam('id', $casesId, \PDO::PARAM_INT);
        $stmt->bindParam('uuid', $domainId, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function queryCSGOSonata()
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.casesSkins', 'cs')
            ->where($qb->expr()->isNotNull('cs.id'));

        return $qb;
    }

    public function queryPUBGSonata()
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->leftJoin('c.casesSkinsPubg', 'cs')
            ->where($qb->expr()->isNotNull('cs.id'));

        return $qb;
    }
}