<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 31.05.17
 * Time: 21:37
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Repository\SkinsPriceRepository;
use Predis\Client;
use SteamBundle\SteamMethod\Inventory;

class SteamUserInventoryHandler
{
    /**
     * @var SkinsPriceRepository
     */
    private $productPriceRepository;

    /**
     * @var Inventory
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
     * @param SkinsPriceRepository $productPriceRepository
     * @param Inventory $inventoryBuilderDota
     * @param Client $redis
     * @param $steamCommunityImageUrl
     */
    public function __construct(
        SkinsPriceRepository $productPriceRepository,
        Inventory $inventoryBuilderDota,
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
     * @param int $gameId
     * @return array
     */
    public function handler(User $user, $page, $gameId)
    {
        $userInventory = $this->getInventory($user, $page, $gameId);
        $sellUserProducts = $this->productPriceRepository
            ->findSkinsPriceByUserArrayResult($user->getId());

        foreach ($sellUserProducts as $product) {
            $key = "{$product['class_id']}:{$product['instance_id']}:{$product['asset_id']}";
            if (isset($userInventory[$key])) {
                unset($userInventory[$key]);
            }
        }

        return $userInventory;
    }

    /**
     * @param User $user
     * @param $page
     * @param $gameId
     * @return mixed
     */
    private function getInventory(User $user, $page, $gameId)
    {
        $keyItem = "user:inventory:{$gameId}:{$user->getId()}";
        $keyStartId = "user:inventory:{$gameId}:{$user->getId()}:start_id";

        $userInventory = json_decode($this->redis->hget($keyItem, $page), 1);
        if ($userInventory) {
            return $userInventory;
        }

        $userInventory = $this->inventoryBuilderDota
            ->getResult($user->getSteamId(), (int) $this->redis->get($keyStartId), $gameId);

        if (!isset($userInventory['descriptions'])) {
            return [];
        }

        $descriptionItem = [];
        foreach ($userInventory['descriptions'] as $description) {
            $descriptionItem[$description['classid']] = [
                'type_skins' => $this->getDictionaryType($description['tags'], 0),
                'weapon' => $this->getDictionaryType($description['tags'], 1),
                'item_set' => $this->getDictionaryType($description['tags'], 2),
                'quality' => $this->getDictionaryType($description['tags'], 3),
                'rarity' => $this->getDictionaryType($description['tags'], 4),
                'decor' => $this->getDictionaryType($description['tags'], 5),
                'is_sell' => $description['marketable'],
                'name' => $description['name'],
                'descriptions' => $description['descriptions'][2]['value'],
                'icon_url' => $this->steamCommunityImageUrl . $description['icon_url'],
                'icon_url_large' => $this->steamCommunityImageUrl . $description['icon_url_large'],
            ];
        }

        $userItem = [];
        foreach ($userInventory['assets'] as $asset) {
            $userItem["{$asset['classid']}:{$asset['instanceid']}:{$asset['assetid']}"] = $descriptionItem[$asset['classid']];
        }

        uasort($userItem, function ($a) {
            return $a['is_sell'] == 0;
        });

        $this->redis->hsetnx($keyItem, $page, json_encode($userItem));
        if (isset($userInventory['last_assetid'])) {
            $lastAssetId = $userInventory['last_assetid'];
            $this->redis->set($keyStartId, $lastAssetId);
        }

        return $userItem;
    }

    /**
     * @param $item
     * @param $key
     * @return array
     */
    private function getDictionaryType($item, $key)
    {
        if (isset($item[$key])) {
            $tag = [
                'internal_name' => $item[$key]['internal_name'],
                'localized_tag_name' => $item[$key]['localized_tag_name'],
            ];

            if (isset($item['color'])) {
                $tag['color'] = $item[$key]['color'];
            }
        } else {
            $tag = [
                'internal_name' => 'another',
                'localized_tag_name' => 'Другое',
            ];
        }

        return $tag;
    }
}