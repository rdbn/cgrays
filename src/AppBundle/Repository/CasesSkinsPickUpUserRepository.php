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
     * @param $userId
     * @param $domainId
     * @return array
     */
    public function findSkinsByUserIdAndDomainId($userId, $domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT 
           s.name, s.icon_url_large, s.steam_price
        FROM cases_skins_pick_up_user cspuu
          LEFT JOIN skins s ON cspuu.skins_id = s.id
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