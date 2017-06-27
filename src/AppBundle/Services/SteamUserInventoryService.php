<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 31.05.17
 * Time: 21:37
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Repository\ProductPriceRepository;
use Predis\Client;
use SteamBundle\DotaMethod\InventoryBuilderDotaMethod;

class SteamUserInventoryService
{
    /**
     * @var ProductPriceRepository
     */
    private $productPriceRepository;

    /**
     * @var InventoryBuilderDotaMethod
     */
    private $inventoryBuilderDota;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var string
     */
    private $steamCommunityImageUrl;

    /**
     * SteamUserInventoryService constructor.
     * @param ProductPriceRepository $productPriceRepository
     * @param InventoryBuilderDotaMethod $inventoryBuilderDota
     * @param Client $redis
     * @param $steamCommunityImageUrl
     */
    public function __construct(
        ProductPriceRepository $productPriceRepository,
        InventoryBuilderDotaMethod $inventoryBuilderDota,
        Client $redis,
        $steamCommunityImageUrl
    )
    {
        $this->productPriceRepository = $productPriceRepository;
        $this->inventoryBuilderDota = $inventoryBuilderDota;
        $this->redis = $redis;
        $this->steamCommunityImageUrl = $steamCommunityImageUrl;
    }

    /**
     * @param User $user
     * @param int $page
     * @return array
     */
    public function handler(User $user, $page)
    {
        $userCacheInventory = $this->getInventoryCache($user, $page);

        $userInventory = [];
        foreach ($userCacheInventory as $item) {
            $userInventory[$item['classid'] . '-' . $item['instanceid']] = [
                'classid' => $item['classid'],
                'instanceid' => $item['instanceid'],
                'quality' => $item['tags'][0]['localized_tag_name'],
                'rarity' => $item['tags'][1]['localized_tag_name'],
                'type_product' => $item['tags'][2]['localized_tag_name'],
                'heroes' => $item['tags'][4]['localized_tag_name'],
                'is_sell' => $item['marketable'],
                'name' => $item['name'],
                'descriptions' => $item['descriptions'][0]['value'],
                'icon_url' => $this->steamCommunityImageUrl . $item['icon_url'],
                'icon_url_large' => $this->steamCommunityImageUrl . $item['icon_url_large'],
            ];
        }

        uasort($userInventory, function ($a) {
            return $a['is_sell'] == 0;
        });

        return $this->clearAddSellItemInventory($user->getId(), $userInventory);
    }

    /**
     * @param User $user
     * @param $page
     * @return mixed
     */
    private function getInventoryCache(User $user, $page)
    {
        $userCacheInventory = json_decode($this->redis->hget("user:inventory:{$user->getId()}", $page), 1);
        if (empty($userCacheInventory)) {
            $startId = $this->redis->get("user:inventory:{$user->getId()}:start_id");
            $userInventory = $this->inventoryBuilderDota
                ->getResult($startId);

            $userCacheInventory = $userInventory['descriptions'];
            $this->redis->hsetnx("user:inventory:{$user->getId()}", $page, json_encode($userCacheInventory));

            if (isset($userInventory['last_assetid'])) {
                $lastAssetid = $userInventory['last_assetid'];
                $this->redis->set("user:inventory:{$user->getId()}:start_id", $lastAssetid);
            }
        }

        return $userCacheInventory;
    }

    /**
     * @param $userId
     * @param $userInventory
     * @return mixed
     */
    private function clearAddSellItemInventory($userId, $userInventory)
    {
        $sellUserProducts = $this->productPriceRepository
            ->findProductsByUserArrayResult($userId);

        foreach ($sellUserProducts as $product) {
            $key = $product['class_id'] . '-' . $product['instance_id'];
            if (isset($userInventory[$key])) {
                unset($userInventory[$key]);
            }
        }

        return $userInventory;
    }
}