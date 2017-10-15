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
}