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
     * @param int $categoryId
     * @return array
     */
    public function findCasesByCategoryId($domainId, $categoryId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->addSelect('cs')
            ->leftJoin('c.casesDomain', 'cd')
            ->leftJoin('c.casesSkins', 'cs')
            ->leftJoin('c.casesCategory', 'cc')
            ->where('cc.id = :categoryId')
            ->andWhere('cd.uuid = :uuid')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('uuid', $domainId);

        return $qb->getQuery()->getResult();
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