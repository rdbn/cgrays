<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 31.05.17
 * Time: 21:37
 */

namespace AppBundle\Services;

use AppBundle\Entity\Heroes;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeProduct;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Predis\Client;
use Psr\Log\LoggerInterface;

class SaveSellProductService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var UploadImageService
     */
    private $uploadImage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SteamUserInventoryService constructor.
     * @param EntityManager $em
     * @param Client $redis
     * @param UploadImageService $uploadImage
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, Client $redis, UploadImageService $uploadImage, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->uploadImage = $uploadImage;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param array $addItem
     * @throws \Exception
     * @return ProductPrice
     */
    public function handler(User $user, array $addItem)
    {
        $item = [];
        $userCacheInventory = json_decode($this->redis->hget("user:inventory:{$user->getId()}", $addItem['page']), 1);
        $itemIds = explode("-", $addItem['id']);
        foreach ($userCacheInventory as $value) {
            if ($itemIds[0] == $value['classid'] && $itemIds[1] == $value['instanceid']) {
                $item = $value;
                break;
            }
        }

        if (!(bool) $item['marketable']) {
            throw new \Exception("Данный предмет нельзя выставить на продажу.");
        }

        $item['price'] = $addItem['price'];
        $product = $this->em->getRepository(Product::class)
            ->findOneBy(['instanceId' => $item['instanceid'], 'classId' => $item['classid']]);

        if (!$product) {
            $product = $this->insertProduct($item);
        }

        return $this->insertProductPrice($user, $product, $item);
    }

    /**
     * @param $item
     * @return Product
     */
    private function insertProduct($item)
    {
        $quality = $this->em->getRepository(Quality::class)
            ->findOneBy(['name' => $item['tags'][0]['internal_name']]);

        $rarity = $this->em->getRepository(Rarity::class)
            ->findOneBy(['name' => mb_strtolower($item['tags'][1]['localized_tag_name'])]);

        $typeProduct = $this->getTypeProduct( $item['tags'][2]);

        $heroes = $this->em->getRepository(Heroes::class)
            ->findOneBy(['name' => $item['tags'][4]['internal_name']]);

        try {
            $product = new Product();
            $product->setInstanceId($item['instanceid']);
            $product->setClassId($item['classid']);
            $product->setIconUrlLarge($this->uploadImage->upload($item['icon_url_large']));
            $product->setIconUrl($this->uploadImage->upload($item['icon_url']));
            $product->setName($item['name']);
            $product->setDescription($item['descriptions'][0]['value']);

            $product->setRarity($rarity);
            $product->setQuality($quality);
            $product->setTypeProduct($typeProduct);
            $product->setHeroes($heroes);

            $this->em->persist($product);
            $this->em->flush();

            return $product;
        } catch (\Exception $e) {
            $this->logger->error(json_encode($item) . $e->getMessage());
        }
    }

    /**
     * @param User $user
     * @param Product $product
     * @param $item
     * @return ProductPrice
     */
    private function insertProductPrice(User $user, Product $product, $item)
    {
        try {
            $productPrice = new ProductPrice();
            $productPrice->setUsers($user);
            $productPrice->setProduct($product);
            $productPrice->setPrice($item['price']);

            $this->em->persist($productPrice);
            $this->em->flush();

            return $productPrice;
        } catch (\Exception $e) {
            $this->logger->error(json_encode($item) . $e->getMessage());
        }
    }

    private function getTypeProduct($type)
    {
        $typeProduct = $this->em->getRepository(TypeProduct::class)
            ->findOneBy(['name' => $type['internal_name']]);

        if (!$typeProduct) {
            try {
                $typeProduct = new TypeProduct();
                $typeProduct->setName($type['internal_name']);
                $typeProduct->setTitle($type['localized_tag_name']);

                $this->em->persist($typeProduct);
                $this->em->flush();
            } catch (\Exception $e) {
                $this->logger->error(json_encode($type) . $e->getMessage());
            }
        }

        return $typeProduct;
    }
}
