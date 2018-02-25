<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesSkinsPickUpUserRepository extends EntityRepository
{
    /**
     * @param $ids
     * @return array
     */
    public function findSkinsForUpdateByIds($ids)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT cspuu.* FROM cases_skins_pick_up_user cspuu WHERE cspuu.id IN (:ids) FOR UPDATE
        ");
        $stmt->bindParam('ids', $ids, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $domainId
     * @param $userId
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function queryPaginationByDomainIdAndUserId($domainId, $userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();
        $qb
            ->addSelect('s.name as skin_name')
            ->addSelect('s.icon_url as steam_image')
            ->addSelect('s.steam_price as price')
            ->addSelect('s.rarity_id')
            ->addSelect('w.localized_tag_name as weapon_name')
            ->from('cases_skins_pick_up_user', 'cspuu')
            ->leftJoin('cspuu', 'skins', 's', 'cspuu.skins_id = s.id')
            ->leftJoin('cspuu', 'weapon', 'w', 's.weapon_id = w.id')
            ->leftJoin('cspuu', 'cases_domain', 'cd', 'cspuu.cases_domain_id = cd.id')
            ->andWhere($qb->expr()->eq('cspuu.user_id', $userId))
            ->andWhere($qb->expr()->eq('cd.uuid', "'{$domainId}'"))
            ->orderBy('cspuu.created_at', 'DESC');

        return $qb;
    }

    /**
     * @param $userId
     * @param $domainId
     * @return array
     */
    public function findSkinsByUserIdAndDomainId($userId, $domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT 
          s.name as skin_name, 
          s.icon_url as steam_image, 
          s.steam_price as price, 
          w.localized_tag_name as weapon_name,
          s.rarity_id
        FROM cases_skins_pick_up_user cspuu
          LEFT JOIN skins s ON cspuu.skins_id = s.id
          LEFT JOIN weapon w ON s.weapon_id = w.id
          LEFT JOIN cases_domain cd ON cspuu.cases_domain_id = cd.id
        WHERE 
          cspuu.user_id = :user_id
          AND cd.uuid = :domain_id
        ");
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam('domain_id', $domainId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}