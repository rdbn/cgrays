<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserPickUpSkinsSteamPUBGRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class UserPickUpSkinsSteamPUBGRepository extends EntityRepository
{
    /**
     * @param $domainId
     * @return array
     */
    public function findListPickUpSkinsByDomainId($domainId)
    {
        $qb = $this->createQueryBuilder('upss');
        $qb
            ->leftJoin('upss.casesDomain', 'cc')
            ->where('cc.uuid = :domain_id')
            ->setParameter('domain_id', $domainId);

        return $qb->getQuery()->getResult();
    }
}
