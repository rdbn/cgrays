<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 31.05.17
 * Time: 21:37
 */

namespace AppBundle\Services\SellSkins;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\User;
use AppBundle\Entity\Weapon;
use AppBundle\Services\UploadImageService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Predis\Client;
use Psr\Log\LoggerInterface;

class AddSellHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Connection
     */
    private $dbal;

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

    private $iconUrl;

    /**
     * SteamUserInventoryService constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param Client $redis
     * @param UploadImageService $uploadImage
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, Connection $dbal, Client $redis, UploadImageService $uploadImage, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->redis = $redis;
        $this->uploadImage = $uploadImage;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param array $addItem
     * @param int $gameId
     * @throws \Exception
     * @return SkinsPrice
     */
    public function handler(User $user, array $addItem, $gameId)
    {
        $userCacheInventory = json_decode($this->redis->hget(
            "user:inventory:{$gameId}:{$user->getId()}",
            $addItem['page']),
            1
        );

        $ids = explode(':', $addItem['id']);
        $item = $userCacheInventory[$addItem['id']];
        $item['classid'] = $ids[0];
        $item['instanceid'] = $ids[1];
        $item['assetid'] = $ids[2];
        $item['price'] = $addItem['price'];

        if (!(bool) $item['is_sell']) {
            throw new \Exception("Данный предмет нельзя выставить на продажу.");
        }
        $item['id'] = $this->getSkinsPrice($user, $item);
        $item['icon_url'] = $this->iconUrl;

        return $item;
    }

    /**
     * @param $entity
     * @param $tableName
     * @param $item
     * @throws \Exception
     * @return int
     */
    private function getDictionary($entity, $tableName, $item)
    {
        $dictionary = $this->em->getRepository($entity)
            ->findOneBy(['localizedTagName' => $item['localized_tag_name']]);

        if ($dictionary) {
            return $dictionary->getId();
        }

        if ($tableName == 'rarity') {
            $item['color'] = $item['color'] ? $item['color'] : 'fff';
        }

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert($tableName, $item);
            $id = $this->dbal->lastInsertId();
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollback();
            throw new \Exception(json_encode($item) . $e->getMessage());
        }

        return $id;
    }

    /**
     * @param $item
     * @return int
     * @throws \Exception
     */
    private function getSkins($item)
    {
        $skins = $this->em->getRepository(Skins::class)
            ->findOneBy(['name' => $item['name']]);

        if ($skins) {
            return $skins->getId();
        }

        $itemSetId = $this->getDictionary(ItemSet::class, 'item_set', $item['item_set']);
        $decorId = $this->getDictionary(Decor::class, 'decor', $item['decor']);
        $weaponId = $this->getDictionary(Weapon::class, 'weapon', $item['weapon']);
        $rarityId = $this->getDictionary(Rarity::class,'rarity', $item['rarity']);
        $typeSkinId = $this->getDictionary(TypeSkins::class,'type_skins', $item['type_skins']);
        $qualityId = $this->getDictionary(Quality::class,'quality', $item['quality']);
        $this->iconUrl = "image/" . $this->uploadImage->upload($item['icon_url'], false);
        $iconUrlLarge = "image/" . $this->uploadImage->upload($item['icon_url_large'], false);

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('skins', [
                'item_set_id' => $itemSetId,
                'decor_id' => $decorId,
                'weapon_id' => $weaponId,
                'rarity_id' => $rarityId,
                'type_skins_id' => $typeSkinId,
                'quality_id' => $qualityId,
                'icon_url' => $this->iconUrl,
                'icon_url_large' => $iconUrlLarge,
                'name' => $item['name'],
                'description' => $item['descriptions'],
            ]);
            $id = $this->dbal->lastInsertId();
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollback();
            throw new \Exception(json_encode($item) . $e->getMessage());
        }

        return $id;
    }

    /**
     * @param User $user
     * @param $item
     * @return int
     * @throws \Exception
     */
    private function getSkinsPrice(User $user, $item)
    {
        $skinsPrice = $this->em->getRepository(SkinsPrice::class)
            ->findOneBy([
                'users' => $user,
                'classId' => $item['classid'],
                'instanceId' => $item['instanceid'],
                'assetId' => $item['assetid'],
            ]);

        if ($skinsPrice) {
            $skinsPrice->setPrice($item['price']);
            $skinsPrice->setIsSell(true);
            $this->em->flush();

            $this->iconUrl = $skinsPrice->getSkins()->getIconUrl();
            return $skinsPrice->getId();
        }

        $skinId = $this->getSkins($item);
        $date = new \DateTime();

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('skins_price', [
                'instance_id' => $item['instanceid'],
                'class_id' => $item['classid'],
                'asset_id' => $item['assetid'],
                'user_id' => $user->getId(),
                'skins_id' => $skinId,
                'price' => $item['price'],
                'created_at' => $date->format('Y-m-d H:i:s'),
            ]);
            $id = $this->dbal->lastInsertId();
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollback();
            throw new \Exception(json_encode($item) . $e->getMessage());
        }

        return $id;
    }
}
