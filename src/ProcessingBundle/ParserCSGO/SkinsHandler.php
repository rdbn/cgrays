<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.10.17
 * Time: 17:18
 */

namespace ProcessingBundle\ParserCSGO;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use AppBundle\Modal\DictionaryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use ProcessingBundle\ParserCSGO\Helper\FilterSkinHelper;

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
     * SkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     */
    public function __construct(EntityManager $em, Connection $dbal)
    {
        $this->em = $em;
        $this->dbal = $dbal;
    }

    /**
     * @param $jsStringSkin
     * @param $price
     * @throws \Exception
     */
    public function handler($jsStringSkin, $price)
    {
        preg_match('/var g_rgAssets = ({.*})/i', $jsStringSkin, $matches);
        $skin = json_decode($matches[1], 1);
        $skin = array_shift($skin);
        $skin = array_shift($skin);
        $skin = array_shift($skin);

        $this->addSkin($skin, $price);
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
     * @param $price
     * @throws \Exception
     */
    private function addSkin(array $skin, $price)
    {
        $filter = FilterSkinHelper::normalize($skin);

        $decorId = $this->addDictionary(Decor::class, 'decor', $filter['decor']);
        $typeSkinsId = $this->addDictionary(TypeSkins::class, 'type_skins', $filter['type_skins']);
        $itemSetId = $this->addDictionary(ItemSet::class, 'item_set', $filter['item_set']);
        $rarityId = $this->addDictionary(Rarity::class, 'rarity', $filter['rarity']);
        $qualityId = $this->addDictionary(Quality::class, 'quality', $filter['quality']);
        $weaponId = $this->addDictionary(Weapon::class, 'weapon', $filter['weapon']);

        $filter['name'] = $skin['name'];
        $skin = $this->em->getRepository(Skins::class)
            ->findOneSkinsByFilter($filter);

        if ($skin) {
            return;
        }

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

        $this->dbal->insert('skins', [
            'decor_id' => $decorId,
            'quality_id' => $qualityId,
            'type_skins_id' => $typeSkinsId,
            'item_set_id' => $itemSetId,
            'rarity_id' => $rarityId,
            'weapon_id' => $weaponId,
            'icon_url' => '',
            'icon_url_large' => '',
            'name' => $skin['name'],
            'description' => $description,
            'steam_price' => $price,
        ]);
    }
}