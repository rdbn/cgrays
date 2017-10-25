<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 02.05.17
 * Time: 20:52
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class SkinsPriceRepository extends EntityRepository
{
    /**
     * @param array $data
     * @param string $sort
     * @param string $order
     * @return mixed
     */
    public function querySkinsByFilters(array $data, $sort = null, $order = null)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qbProduct = $dbal->createQueryBuilder();

        $qbProduct
            ->addSelect("DISTINCT ON (s.id) skins_id as skins_id")
            ->addSelect("s.name")
            ->addSelect("s.icon_url")
            ->addSelect("sp.id price_id")
            ->addSelect("sp.price")
            ->addSelect("u.id as user_id")
            ->from("skins", "s");

        if (isset($data["name"])) {
            $qbProduct->andWhere($qbProduct->expr()->like("s.name", "'%{$data["name"]}%'"));
        }

        if (isset($data["typeSkins"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.type_skins_id', (int)$data["typeSkins"]->getId()));
        }

        if (isset($data["decor"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.decor_id', (int)$data["decor"]->getId()));
        }

        if (isset($data["rarity"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.rarity_id', (int)$data["rarity"]->getId()));
        }

        if (isset($data["itemSet"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.item_set_id', (int)$data["itemSet"]->getId()));
        }

        if (isset($data["weapon"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.weapon_id', (int)$data["weapon"]->getId()));
        }

        if (isset($data["quality"])) {
            $qbProduct->andWhere($qbProduct->expr()->eq('s.quality_id', (int)$data["quality"]->getId()));
        }

        $qbProduct
            ->leftJoin('s', 'skins_price', 'sp', 'sp.skins_id = s.id')
            ->leftJoin('sp', 'users', 'u', 'sp.user_id = u.id')
            ->andWhere('u.is_sell = TRUE')
            ->andWhere('sp.is_sell = TRUE')
            ->andWhere('sp.is_remove = FALSE')
            ->andWhere('sp.id IS NOT NULL');


        $qb = $dbal->createQueryBuilder();
        $qb
            ->select('skins.*')
            ->from("({$qbProduct->getSQL()})", 'skins');

        if ($sort) {
            $qb->orderBy("skins.{$sort}", $order);
        }

        return $qb;
    }

    /**
     * @param int $id
     * @param string $sort
     * @param string $order
     * @return mixed
     */
    public function querySkinsPrice($id, $sort = null, $order = null)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("sp.id as price_id")
            ->addSelect("s.id as skins_id")
            ->addSelect("s.name")
            ->addSelect("s.icon_url")
            ->addSelect("sp.price")
            ->from("skins_price", "sp")
            ->leftJoin("sp" , "skins", "s", "s.id = sp.skins_id")
            ->andWhere("sp.skins_id = :id")
            ->andWhere('sp.is_sell = TRUE')
            ->andWhere('sp.is_remove = FALSE')
            ->setParameter("id", $id);

        if ($sort == 'price') {
            $qb->orderBy("pr.price", $order);
        }

        return $qb;
    }

    /**
     * @param int $skinsId
     * @param int $skinsPriceId
     * @return mixed
     */
    public function findSkinsPriceBySkinsId($skinsId, $skinsPriceId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("sp.id as price_id")
            ->addSelect("s.icon_url")
            ->addSelect("sp.price")
            ->from("skins_price", "sp")
            ->leftJoin("sp" , "skins", "s", "s.id = sp.skins_id")
            ->andWhere("sp.skins_id = :skins_id")
            ->andWhere("sp.id <> :skins_price_id")
            ->andWhere('sp.is_sell = TRUE')
            ->andWhere('sp.is_remove = FALSE');

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam('skins_id', $skinsId, \PDO::PARAM_INT);
        $stmt->bindParam('skins_price_id', $skinsPriceId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param User $user
     * @return array
     */
    public function findSkinsPriceByUser(User $user)
    {
        $qb = $this->createQueryBuilder("sp");
        $qb
            ->where("sp.users = :user")
            ->andWhere("sp.isSell = true")
            ->andWhere("sp.isRemove = false")
            ->setParameter("user", $user);

        try {
            return $qb->getQuery()->getResult();
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * @param $userId
     * @return array
     */
    public function findSkinsPriceByUserArrayResult($userId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();
        $qb
            ->addSelect('sp.class_id')
            ->addSelect('sp.instance_id')
            ->addSelect('sp.asset_id')
            ->from('skins_price', 'sp')
            ->where('sp.user_id = :user_id')
            ->andWhere("sp.is_sell = TRUE")
            ->andWhere("sp.is_remove = FALSE");

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam('user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int $skinsPriceId
     * @return mixed
     */
    public function findSkinsPriceById($skinsPriceId)
    {
        $qb = $this->createQueryBuilder("sp");
        $qb
            ->join('sp.skins', 's')
            ->join("s.typeSkins", "ts")
            ->join("s.quality", "q")
            ->andWhere("sp.id = :skins_price_id")
            ->andWhere("sp.isSell = true")
            ->setParameter("skins_price_id", $skinsPriceId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (DBALException $e) {
            return [];
        }
    }

    /**
     * @param $skinsPriceId
     * @return mixed
     */
    public function findSkinsPriceForUpdateBySkinsPriceId($skinsPriceId)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare('
        SELECT id, class_id, instance_id, price FROM skins_price WHERE id = :id FOR UPDATE
        ');
        $stmt->bindParam('id', $skinsPriceId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param $skinsPriceIds
     * @return mixed
     */
    public function findSkinsPriceForUpdateBySkinsPriceIds($skinsPriceIds)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("
        SELECT id, class_id, instance_id, price FROM skins_price WHERE id IN ({$skinsPriceIds}) FOR UPDATE
        ");
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param $skinsPriceIds
     * @return mixed
     */
    public function findSumSkinsPriceBySkinsPriceIds($skinsPriceIds)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $stmt = $dbal->prepare("SELECT SUM(price) as all_sum FROM skins_price WHERE id IN ({$skinsPriceIds})");
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    public function querySonata()
    {
        $qb = $this->createQueryBuilder('sp');
        $qb
            ->addSelect('s')
            ->addSelect('r')
            ->addSelect('q')
            ->addSelect('w')
            ->addSelect('its')
            ->addSelect('ts')
            ->addSelect('d')
            ->leftJoin('sp.skins', 's')
            ->leftJoin('s.rarity', 'r')
            ->leftJoin('s.quality', 'q')
            ->leftJoin('s.weapon', 'w')
            ->leftJoin('s.itemSet', 'its')
            ->leftJoin('s.typeSkins', 'ts')
            ->leftJoin('s.decor', 'd');

        return $qb;
    }
}