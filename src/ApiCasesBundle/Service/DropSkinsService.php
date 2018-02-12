<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 06.02.2018
 * Time: 11:11
 */

namespace ApiCasesBundle\Service;


class DropSkinsService
{
    const LIMIT_DROP = 100;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * DropSkinsService constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param array $skins
     * @param $casesId
     * @return array
     */
    public function getDropSkins(array $skins, $casesId)
    {
        $procentRarity = [];
        $procentSkins = [];
        foreach ($skins as $skin) {
            $procentRarity[$skin['rarity_id']] = $skin['procent_rarity'];
            $procentSkins[$skins['id']] = $skin['procent_skins'];
        }

        $casesSkinsDrops = json_decode($this->redis->get("cases_skins_drops_{$casesId}"), 1);
        $countDrop = count($casesSkinsDrops);
        if ($countDrop <= self::LIMIT_DROP) {
            #todo: Formula
            $countDrop = self::LIMIT_DROP - $countDrop;
            foreach ($procentRarity as $item) {

            }
        } else {
            #todo: Formula
        }

        return $skins[0];
    }
}