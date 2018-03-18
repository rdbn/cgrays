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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAllSkinsByFilter($dataFilters, $offset, $limit)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();
        $qb
            ->addSelect('s.id as skins_id')
            ->addSelect('s.name')
            ->addSelect('s.icon_url')
            ->addSelect('s.rarity_id')
            ->addSelect('s.steam_price as price')
            ->from('skins', 's')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if (isset($dataFilters['typeSkins'])) {
            $qb->andWhere($qb->expr()->eq('s.type_skins_id', (int) $dataFilters['typeSkins']->getId()));
        }

        if (isset($dataFilters['decor'])) {
            $qb->andWhere($qb->expr()->eq('s.decor_id', (int) $dataFilters['decor']->getId()));
        }

        if (isset($dataFilters['rarity'])) {
            $qb->andWhere($qb->expr()->eq('s.rarity_id', (int) $dataFilters['rarity']->getId()));
        }

        if (isset($dataFilters['itemSet'])) {
            $qb->andWhere($qb->expr()->eq('s.item_set_id', (int) $dataFilters['itemSet']->getId()));
        }

        if (isset($dataFilters['weapon'])) {
            $qb->andWhere($qb->expr()->eq('s.weapon_id', (int) $dataFilters['weapon']->getId()));
        }

        if (isset($dataFilters['quality'])) {
            $qb->andWhere($qb->expr()->eq('s.quality_id', (int) $dataFilters['quality']->getId()));
        }

        if (isset($dataFilters['name'])) {
            $qb
                ->andWhere("s.name ~* :name");
        }

        $stmt = $dbal->prepare($qb->getSQL());
        if (isset($dataFilters['name'])) {
            $stmt->bindParam('name',$dataFilters['name'], \PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param $skinsName
     * @param $weaponName
     * @param $rarityName
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOneSkinsByNameAndWeaponAndRarity($skinsName, $weaponName, $rarityName)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT s.id FROM skins s
          LEFT JOIN weapon w ON s.weapon_id = w.id
          LEFT JOIN rarity r ON s.rarity_id = r.id
        WHERE
          s.name = :skins_name 
          AND w.localized_tag_name = :weapon_name 
          AND r.localized_tag_name = :rarity_name
        ");
        $stmt->bindParam('skins_name', $skinsName, \PDO::PARAM_STR);
        $stmt->bindParam('weapon_name', $weaponName, \PDO::PARAM_STR);
        $stmt->bindParam('rarity_name', $rarityName, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}