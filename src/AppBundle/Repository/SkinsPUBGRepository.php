<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SkinsPUBGRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class SkinsPUBGRepository extends EntityRepository
{
    public function querySonata()
    {
        $qb = $this->createQueryBuilder('spubg');
        $qb
            ->addSelect('r')
            ->leftJoin('spubg.rarity', 'r');

        return $qb;
    }

    /**
     * @param string $ids
     * @return array
     */
    public function findSkinsByIds($ids)
    {
        $qb = $this->createQueryBuilder('spubg');
        $qb
            ->where($qb->expr()->in('spubg.id', $ids));

        return $qb->getQuery()->getResult();
    }

    /**
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCountSkins()
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("SELECT COUNT(id) as count_skins FROM skins_pubg");
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
            ->addSelect('spubg.id as skins_id')
            ->addSelect('spubg.name')
            ->addSelect('spubg.image as icon_url')
            ->addSelect('spubg.rarity_id')
            ->addSelect('spubg.steam_price as price')
            ->from('skins_pubg', 'spubg')
            ->setFirstResult($offset)
            ->setMaxResults($limit);


        if (isset($dataFilters['rarity'])) {
            $qb->andWhere($qb->expr()->eq('spubg.rarity_id', (int) $dataFilters['rarity']->getId()));
        }

        if (isset($dataFilters['name'])) {
            $qb
                ->andWhere("spubg.name ~* :name");
        }

        $stmt = $dbal->prepare($qb->getSQL());
        if (isset($dataFilters['name'])) {
            $stmt->bindParam('name',$dataFilters['name'], \PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
