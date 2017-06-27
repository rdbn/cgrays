<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param array $data
     * @return mixed
     */
    public function queryProductsByFilters(array $data)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("DISTINCT p.id as product_id")
            ->addSelect("p.name")
            ->addSelect("p.icon_url")
            ->addSelect("(SELECT pr.id FROM product_price pr WHERE pr.product_id = p.id ORDER BY pr.price DESC LIMIT 1 OFFSET 0) as price_id")
            ->addSelect("(SELECT pr.price FROM product_price pr WHERE pr.product_id = p.id ORDER BY pr.price DESC LIMIT 1 OFFSET 0) as price")
            ->from("product", "p");

        if (isset($data["name"])) {
            $qb
                ->andWhere($qb->expr()->like("p.name", ":name"))
                ->setParameter("name", "%{$data["name"]}%");
        }

        if (isset($data["heroes"])) {
            $qb
                ->andWhere("p.heroes_id = :heroes")
                ->setParameter("heroes", $data["heroes"]->getId());
        }

        if (isset($data["typeProduct"])) {
            $qb
                ->andWhere("p.type_product_id = :typeProduct")
                ->setParameter("typeProduct", $data["typeProduct"]->getId());
        }

        if (isset($data["quality"])) {
            $qb
                ->andWhere("p.quality_id = :quality")
                ->setParameter("quality", $data["quality"]->getId());
        }

        if (isset($data["rareness"])) {
            $qb
                ->andWhere("p.rareness_id = :rareness")
                ->setParameter("rareness", $data["rareness"]->getId());
        }

        $qb
            ->leftJoin('p', 'product_price', 'pr', 'pr.product_id = p.id')
            ->andWhere('pr.id IS NOT NULL');

        return $qb;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function queryProductsPrice($id)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $query = $qb
            ->addSelect("DISTINCT pr.id as price_id")
            ->addSelect("p.id as product_id")
            ->addSelect("p.name")
            ->addSelect("p.icon_url_large")
            ->addSelect("pr.price")
            ->from("product", "p")
            ->leftJoin("p" , "product_price", "pr", "p.id = pr.product_id")
            ->andWhere("p.id = :id")
            ->setParameter("id", $id)
            ->orderBy("pr.price", "DESC");

        return $query;
    }

    /**
     * @param int $productId
     * @param int $productPriceId
     * @return mixed
     */
    public function findProductByIdAndPriceId($productId, $productPriceId)
    {
        $qb = $this->createQueryBuilder("p");
        $qb
            ->addSelect("h")
            ->addSelect("tp")
            ->addSelect("q")
            ->addSelect("r")
            ->addSelect("pr")
            ->leftJoin("p.heroes", "h")
            ->leftJoin("p.typeProduct", "tp")
            ->leftJoin("p.quality", "q")
            ->leftJoin("p.rarity", "r")
            ->leftJoin("p.productPrice", "pr")
            ->where("p.id = :product_id")
            ->andWhere("pr.id = :product_price_id")
            ->setParameter("product_id", $productId)
            ->setParameter("product_price_id", $productPriceId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            return [];
        }
    }
}