<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.10.17
 * Time: 17:18
 */

namespace ProcessingBundle\Parser;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use AppBundle\Modal\DictionaryInterface;
use AppBundle\Services\UploadImageService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class SkinsHandler
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
    private $guzzle;

    /**
     * @var UploadImageService
     */
    private $uploadImage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $steamCommunityImageUrl;

    /**
     * SkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param Client $guzzle
     * @param UploadImageService $uploadImage
     * @param LoggerInterface $logger
     * @param LoggerInterface $logger
     * @param $steamCommunityImageUrl
     */
    public function __construct(
        EntityManager $em,
        Connection $dbal,
        Client $guzzle,
        UploadImageService $uploadImage,
        LoggerInterface $logger,
        $steamCommunityImageUrl
    )
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->guzzle = $guzzle;
        $this->uploadImage = $uploadImage;
        $this->logger = $logger;
        $this->steamCommunityImageUrl = $steamCommunityImageUrl;
    }

    /**
     * @param array $skin
     */
    public function handler(array $skin)
    {
        $request = $this->guzzle->request('GET', $skin['link'], [
            'headers' => [
                'Accept-Language' => 'ru,en-US;q=0.8,en;q=0.6,cs;q=0.4,es;q=0.2,de;q=0.2,fr;q=0.2,it;q=0.2,la;q=0.2,und;q=0.2,pl;q=0.2',
            ],
        ]);
        $skinHTML = $request->getBody();

        preg_match('/var g_rgAssets = ({.*})/i', $skinHTML, $matches);
        $skin = json_decode($matches[1], 1);
        $skin = array_shift($skin);
        $skin = array_shift($skin);
        $skin = array_shift($skin);

        $this->addSkin($skin);
    }

    /**
     * @param $entity
     * @param $tableName
     * @param $name
     * @return string
     * @throws \Exception
     */
    private function addDictionary($entity, $tableName, $name)
    {
        /* @var DictionaryInterface $dictionary */
        $dictionary = $this->em->getRepository($entity)
            ->findOneBy(['localizedTagName' => $name]);

        if ($dictionary) {
            return $dictionary->getId();
        }

        $values = ['localized_tag_name' => $name];
        if ($tableName == 'rarity') {
            $values['color'] = '4b69ff';
        }

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert($tableName, $values);
            $id = $this->dbal->lastInsertId();
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $id;
    }

    /**
     * @param array $skin
     * @throws \Exception
     */
    private function addSkin(array $skin)
    {
        $type = explode(",", $skin['type']);
        if (count($type) == 2) {
            $typeSkinsName = trim($type[0]);
            $qualityName = 'Обыч.';
            $rarityName = trim($type[1]);

            preg_match('/(.*)\s\|/i', $skin['name'], $matches);
            $weaponName = trim($matches[1]);
        } else {
            $typeSkinsName = trim($type[0]);
            $qualityName = trim($type[1]);
            $rarityName = trim($type[2]);

            preg_match("/{$qualityName}\s(.*)\s\||/i", $skin['name'], $matches);
            $weaponName = trim($matches[1]);
        }

        if (count($skin['descriptions']) == 7) {
            $decorName = trim(explode(":", $skin['descriptions'][0]['value'])[1]);
            $itemSetName = trim($skin['descriptions'][4]['value']);
        } else {
            $decorName = "Другое";
            $itemSetName = "Другое";
        }

        $decorId = $this->addDictionary(Decor::class, 'decor', $decorName);
        $typeSkinsId = $this->addDictionary(TypeSkins::class, 'type_skins', $typeSkinsName);
        $itemSetId = $this->addDictionary(ItemSet::class, 'item_set', $itemSetName);
        $rarityId = $this->addDictionary(Rarity::class, 'rarity', $rarityName);
        $qualityId = $this->addDictionary(Quality::class, 'quality', $qualityName);
        $weaponId = $this->addDictionary(Weapon::class, 'weapon', $weaponName);
        $iconUrl = "image/" . $this->uploadImage->upload($this->steamCommunityImageUrl . "/" . $skin['icon_url'], false);

        $countDictionary = count($skin['descriptions']);
        switch ($countDictionary) {
            case 7:
                $description = $skin['descriptions'][2]['value'];
                break;
            case 3:
                $description = $skin['descriptions'][0]['value'];
                break;
            default:
                $description = array_map(function ($item) {
                    return $item['value'];
                }, $skin['descriptions']);
                $description = implode(", ", $description);
        }

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('skins', [
                'decor_id' => $decorId,
                'quality_id' => $qualityId,
                'type_skins_id' => $typeSkinsId,
                'item_set_id' => $itemSetId,
                'rarity_id' => $rarityId,
                'weapon_id' => $weaponId,
                'icon_url' => $iconUrl,
                'icon_url_large' => $iconUrl,
                'name' => $skin['name'],
                'description' => $description,
            ]);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());
        }
    }
}