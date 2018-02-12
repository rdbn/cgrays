<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
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
     * @param int $secondDuration
     * @return array
     */
    public function findUserIsNotOnline($secondDuration)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT u.id FROM users u
        WHERE 
          extract(EPOCH FROM current_timestamp - u.last_online) > :second_duration
          AND u.is_not_check_online = FALSE 
          AND u.is_online = FALSE
          AND u.is_sell = TRUE; 
        ');
        $stmt->bindParam('second_duration', $secondDuration, \PDO::PARAM_INT);
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

    /**
     * @param $userId
     * @param $domainId
     * @return mixed
     */
    public function findUserInformationByUserIdAndDomainId($userId, $domainId)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->addSelect('cbu')
            ->leftJoin('u.casesBalanceUser', 'cbu')
            ->leftJoin('cbu.casesDomain', 'cd')
            ->where('u.id = :user_id')
            ->andWhere('cd.uuid = :domain_id')
            ->setParameter('user_id', $userId)
            ->setParameter('domain_id', $domainId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $domainId
     * @return bool|int|string
     */
    public function getCountUserByDomainId($domainId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        try {
            $stmt = $dbal->prepare("SELECT count(u.id) as count_user FROM users u");
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (DBALException $e) {
            return 0;
        }
    }

    /**
     * @param $userIds
     * @return array
     * @throws DBALException
     */
    public function findUsersByUserIds($userIds)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("SELECT u.id as user_id FROM users u WHERE u.id IN ({$userIds})");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}