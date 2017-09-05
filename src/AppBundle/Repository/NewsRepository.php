<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\EntityRepository;

class NewsRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function queryNews()
    {
        $qb = $this->createQueryBuilder('n');

        return $qb;
    }

    public function findCountLikeNewsByNewsId($newsId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT count(nl.user_id) as count_like FROM news_like nl WHERE nl.news_id = :news_id
        ');
        $stmt->bindParam('news_id', $newsId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}