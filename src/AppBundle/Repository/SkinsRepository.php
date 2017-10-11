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

        return $stmt->fetchAll();
    }
}