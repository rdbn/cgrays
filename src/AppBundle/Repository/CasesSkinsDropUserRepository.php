<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class CasesSkinsDropUserRepository extends EntityRepository
{
    /**
     * @param string $domainId
     * @return array
     */
    public function findLastSkinsDrop($domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        try {
            $stmt = $dbal->prepare("
            SELECT
              s.name as skin_name,
              w.localized_tag_name as weapon_name,
              s.icon_url as steam_image,
              s.rarity_id,
              u.id as user_id,
              u.username,
              u.avatar,
              c.image as cases_image,
              csdu.created_at
            FROM cases_skins_drop_user csdu
              LEFT JOIN cases c ON csdu.cases_id = c.id
              LEFT JOIN users u ON csdu.user_id = u.id
              LEFT JOIN skins s ON csdu.skins_id = s.id
              LEFT JOIN weapon w ON s.weapon_id = w.id
              LEFT JOIN cases_domain cd ON csdu.cases_domain_id = cd.id
            WHERE
              cd.uuid = :uuid
            ORDER BY csdu.id DESC
            LIMIT 16 OFFSET 0
            ");
            $stmt->bindParam('uuid', $domainId, \PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * @param string $domainId
     * @return string|boolean
     */
    public function getCountSkinsDrop($domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        try {
            $stmt = $dbal->prepare("
            SELECT count(csdu.id) as count_drop_skins FROM cases_skins_drop_user csdu
              LEFT JOIN cases_domain cd ON csdu.cases_domain_id = cd.id
            WHERE cd.uuid = :uuid
            ");
            $stmt->bindParam('uuid', $domainId, \PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (DBALException $e) {
            return 0;
        }
    }

    /**
     * @param $domainId
     * @param $userId
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCountOpenCasesByDomainIdAndUserId($domainId, $userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT count(csdu.id) as count_drop_skins FROM cases_skins_drop_user csdu
          LEFT JOIN cases_domain cd ON csdu.cases_domain_id = cd.id
        WHERE cd.uuid = :uuid AND csdu.user_id = :user_id
        ');
        $stmt->bindParam('uuid', $domainId, \PDO::PARAM_STR);
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}