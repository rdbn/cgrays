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

class UserRepository extends EntityRepository
{
    /**
     * @param int $userId
     * @return mixed
     */
    public function findUserForUpdateById($userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('SELECT * FROM users WHERE id = :id FOR UPDATE');
        $stmt->bindParam('id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param string $username
     * @return mixed
     */
    public function findUserForUpdateByUsername($username)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('SELECT * FROM users WHERE username = :username FOR UPDATE');
        $stmt->bindParam('username', $username, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function findUserIsNotOnline()
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT u.id FROM users u
        WHERE 
          extract(EPOCH FROM current_timestamp - u.last_online) > 60 
          AND u.is_online = FALSE
          AND u.is_sell = TRUE; 
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}