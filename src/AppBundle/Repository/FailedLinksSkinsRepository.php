<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 17:43
 */

namespace AppBundle\Repository;

use Doctrine\DBAL\Connection;

class FailedLinksSkinsRepository
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * FailedLinksSkinsRepository constructor.
     * @param Connection $dbal
     */
    public function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }

    public function findAllFailedLinks($limit)
    {
        $stmt = $this->dbal->prepare("
        SELECT * FROM a_failed_link_skins LIMIT :limit OFFSET 0
        ");
        $stmt->bindParam("limit", $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}