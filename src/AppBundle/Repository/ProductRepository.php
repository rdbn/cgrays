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
     * @param string $sort
     * @param string $order
     * @return mixed
     */
    public function queryProductsByFilters(array $data, $sort = null, $order = null)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qbProduct = $dbal->createQueryBuilder();

        $qbProduct
            ->addSelect("DISTINCT ON (p.id) product_id")
            ->addSelect("p.name")
            ->addSelect("p.icon_url")
            ->addSelect("pr.id price_id")
            ->addSelect("pr.price")
            ->from("product", "p");

        if (isset($data["name"])) {
            $qbProduct
                ->andWhere($qb->expr()->like("p.name", ":name"))
                ->setParameter("name", "%{$data["name"]}%");
        }

        if (isset($data["heroes"])) {
            $qbProduct
                ->andWhere("p.heroes_id = :heroes")
                ->setParameter("heroes", $data["heroes"]->getId());
        }

        if (isset($data["typeProduct"])) {
            $qbProduct
                ->andWhere("p.type_product_id = :typeProduct")
                ->setParameter("typeProduct", $data["typeProduct"]->getId());
        }

        if (isset($data["quality"])) {
            $qbProduct
                ->andWhere("p.quality_id = :quality")
                ->setParameter("quality", $data["quality"]->getId());
        }

        if (isset($data["rareness"])) {
            $qbProduct
                ->andWhere("p.rareness_id = :rareness")
                ->setParameter("rareness", $data["rareness"]->getId());
        }

        $qbProduct
            ->leftJoin('p', 'product_price', 'pr', 'pr.product_id = p.id')
            ->andWhere('pr.is_sell = TRUE')
            ->andWhere('pr.id IS NOT NULL');


        $qb = $dbal->createQueryBuilder();
        $qb
            ->select('product.*')
            ->from("({$qbProduct->getSQL()})", 'product');

        if ($sort) {
            $qb->orderBy("product.{$sort}", $order);
        }

        return $qb;
    }

    /**
     * @param int $id
     * @param string $sort
     * @param string $order
     * @return mixed
     */
    public function queryProductsPrice($id, $sort = null, $order = null)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();

        $qb
            ->addSelect("pr.id as price_id")
            ->addSelect("p.id as product_id")
            ->addSelect("p.name")
            ->addSelect("p.icon_url")
            ->addSelect("pr.price")
            ->from("product", "p")
            ->leftJoin("p" , "product_price", "pr", "p.id = pr.product_id")
            ->andWhere("p.id = :id")
            ->andWhere('pr.is_sell = TRUE')
            ->setParameter("id", $id);

        if ($sort == 'price') {
            $qb->orderBy("pr.price", $order);
        }

        return $qb;
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
            ->andWhere("pr.isSell = true")
            ->setParameter("product_id", $productId)
            ->setParameter("product_price_id", $productPriceId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            return [];
        }
    }
}