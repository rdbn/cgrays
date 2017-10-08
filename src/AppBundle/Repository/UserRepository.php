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
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /**
     * @param $value
     * @return mixed
     */
    public function findUserByUsernameOrSteamId($value)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.steamId = :user_1')
            ->orWhere('u.username = :user_2')
            ->setParameter('user_1', (int)$value, \PDO::PARAM_INT)
            ->setParameter('user_2', $value, \PDO::PARAM_STR);

        return $qb->getQuery()->getOneOrNullResult();
    }

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

    /**
     * @return array
     */
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

    /**
     * @return QueryBuilder
     */
    public function querySonata()
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->addSelect('r')
            ->leftJoin('u.roles', 'r');

        return $qb;
    }
}