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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param int $secondDuration
     * @return array
     * @throws DBALException
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
     * @return User
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
            return $qb->getQuery()->getOneOrNullResult();
        } catch (\Exception $e) {
            return null;
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
     * @param $userId
     * @return array
     * @throws DBALException
     */
    public function findUsersByUserId($userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT u.username, u.avatar, u.steam_id, count(csdu.id) as count_drop_skins FROM users u
          LEFT JOIN cases_skins_drop_user csdu ON csdu.user_id = u.id
        WHERE u.id = :user_id
        GROUP BY u.id;
        ");
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}