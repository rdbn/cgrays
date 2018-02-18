<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesStaticPageRepository extends EntityRepository
{
    /**
     * @param $domainId
     * @param $typePage
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findStaticPageByDomainIdAndPageName($domainId, $typePage)
    {
        $qb = $this->createQueryBuilder('csp');
        $qb
            ->select('csp.typePage')
            ->addSelect('csp.id')
            ->addSelect('csp.text')
            ->leftJoin('csp.casesDomain', 'cd')
            ->where('cd.uuid = :domain_id')
            ->andWhere('csp.typePage = :type_page')
            ->setParameter('domain_id', $domainId)
            ->setParameter('type_page', $typePage);

        return $qb->getQuery()->getOneOrNullResult();
    }
}