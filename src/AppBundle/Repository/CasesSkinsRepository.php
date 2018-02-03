<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CasesSkinsRepository extends EntityRepository
{
    /**
     * @param $casesId
     * @return array
     */
    public function findCasesSkinsByCasesId($casesId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT
          cs.id, cs.skins_id, cs.procent_rarity, cs.procent_skins, s.name, s.icon_url, s.rarity_id
        FROM cases_skins cs
          LEFT JOIN skins s ON s.id = cs.skins_id
        WHERE
          cs.cases_id = :cases_id;
        ');
        $stmt->bindParam('cases_id', $casesId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $casesSkinsId
     * @return array
     */
    public function findCasesSkinsByCasesIdForUpdate($casesSkinsId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('SELECT * FROM cases_skins cs WHERE cs.id = :id FOR UPDATE;');
        $stmt->bindParam('id', $casesSkinsId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $domainId
     * @param $casesId
     * @return array
     */
    public function findSkinsByCasesId($domainId, $casesId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT
          s.id, 
          s.name, 
          s.icon_url, 
          cs.id as cases_skins_id, 
          cs.procent_rarity, 
          cs.procent_skins, 
          cs.count, 
          cd.id as cases_domain_id,
          w.localized_tag_name as weapon,
          r.localized_tag_name as rarity,
          c.price as cases_price
        FROM cases_skins cs
          LEFT JOIN cases c ON cs.cases_id = c.id
          LEFT JOIN cases_domain cd ON c.cases_domain_id = cd.id
          LEFT JOIN skins s ON s.id = cs.skins_id
          LEFT JOIN weapon w ON w.id = s.weapon_id
          LEFT JOIN rarity r ON r.id = s.rarity_id
        WHERE
           cd.uuid = :uuid AND cs.cases_id = :cases_id
        GROUP BY s.id, cd.id, cs.id, w.localized_tag_name, r.localized_tag_name, c.price;
        ');
        $stmt->bindParam('cases_id', $casesId, \PDO::PARAM_INT);
        $stmt->bindParam('uuid', $domainId, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}