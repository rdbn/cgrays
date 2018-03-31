<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 20.03.2018
 * Time: 10:49
 */

namespace ProcessingBundle\ParserCSGO\Helper;


class FilterSkinHelper
{
    public static function normalize(array $skin)
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

        if ($typeSkinsName == "Наклейка") {
            $decorName = "Другое";
            $itemSetName = "Другое";
        } else if (count($skin['descriptions']) == 7) {
            $decorName = trim(explode(":", $skin['descriptions'][0]['value'])[1]);
            $itemSetName = trim($skin['descriptions'][4]['value']);
        } else {
            $decorName = "Другое";
            $itemSetName = "Другое";
        }

        return [
            'weapon' => $weaponName,
            'decor' => $decorName,
            'quality' => $qualityName,
            'rarity' => $rarityName,
            'item_set' => $itemSetName,
            'type_skins' => $typeSkinsName,
        ];
    }
}