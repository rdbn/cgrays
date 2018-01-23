<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SkinsRepository extends EntityRepository
{
    public function querySonata()
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->addSelect('d')
            ->addSelect('q')
            ->addSelect('w')
            ->addSelect('its')
            ->addSelect('r')
            ->addSelect('ts')
            ->leftJoin('s.decor', 'd')
            ->leftJoin('s.quality', 'q')
            ->leftJoin('s.weapon', 'w')
            ->leftJoin('s.itemSet', 'its')
            ->leftJoin('s.rarity', 'r')
            ->leftJoin('s.typeSkins', 'ts');

        return $qb;
    }

    /**
     * @param string $ids
     * @return array
     */
    public function findSkinsByIds($ids)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where($qb->expr()->in('s.id', $ids));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $namesSkins
     * @return array
     */
    public function findSkinsByNames($namesSkins)
    {
        $dbal = $this->getEntityManager()->getConnection();

        $stmt = $dbal->prepare('SELECT s.id, s.name FROM skins s WHERE s.name IN (:names_skins)');
        $stmt->bindParam('names_skins', $namesSkins);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $skinsId
     * @return array
     */
    public function findSkinsForUpdateById(int $skinsId)
    {
        $dbal = $this->getEntityManager()->getConnection();

        $stmt = $dbal->prepare('SELECT s.* FROM skins s WHERE s.id = :skins_id FOR UPDATE');
        $stmt->bindParam('skins_id', $skinsId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCountSkins()
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("SELECT COUNT(id) as count_skins FROM skins");
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * @param $dataFilters
     * @param $offset
     * @param $limit
     * @return array
     */
    public function findAllSkinsByFilter($dataFilters, $offset, $limit)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if (isset($dataFilters['name'])) {
            $qb
                ->andWhere('s.name = :name')
                ->setParameter('name', $dataFilters['name']);
        }

        if (isset($dataFilters['typeSkins'])) {
            $qb
                ->andWhere('s.typeSkins = :type_skins')
                ->setParameter('type_skins', $dataFilters['typeSkins']);
        }

        if (isset($dataFilters['decor'])) {
            $qb
                ->andWhere('s.decor = :decor')
                ->setParameter('decor', $dataFilters['decor']);
        }

        if (isset($dataFilters['rarity'])) {
            $qb
                ->andWhere('s.rarity = :rarity')
                ->setParameter('rarity', $dataFilters['rarity']);
        }

        if (isset($dataFilters['itemSet'])) {
            $qb
                ->andWhere('s.itemSet = :item_set')
                ->setParameter('item_set', $dataFilters['itemSet']);
        }

        if (isset($dataFilters['weapon'])) {
            $qb
                ->andWhere('s.weapon = :weapon')
                ->setParameter('weapon', $dataFilters['weapon']);
        }

        if (isset($dataFilters['quality'])) {
            $qb
                ->andWhere('s.quality = :quality')
                ->setParameter('quality', $dataFilters['quality']);
        }

        return $qb->getQuery()->getResult();
    }
}