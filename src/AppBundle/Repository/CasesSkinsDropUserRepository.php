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
     * @return array
     */
    public function findLastSkinsDrop()
    {
        $dbal = $this->getEntityManager()->getConnection();
        try {
            $stmt = $dbal->prepare("
            SELECT
              s.name as skin_name,
              w.localized_tag_name as weapon_name,
              s.icon_url as steam_image,
              u.username,
              u.avatar,
              csdu.created_at
            FROM cases_skins_drop_user csdu
              LEFT JOIN users u ON csdu.user_id = u.id
              LEFT JOIN skins s ON csdu.skins_id = s.id
              LEFT JOIN weapon w ON s.weapon_id = w.id
            ORDER BY csdu.id DESC
            LIMIT 6 OFFSET 0
            ");
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            return [];
        }
    }
}