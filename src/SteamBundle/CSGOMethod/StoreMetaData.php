<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 11:00
 */

namespace SteamBundle\CSGOMethod;

use SteamBundle\Modal\AbstractMethod;

/**
 * https://api.steampowered.com/IEconItems_730/GetStoreMetaData/v1/
 *
 * Class HeroesDotaMethod
 * @package SteamBundle\DotaMethod
 */
class StoreMetaData extends AbstractMethod
{
    public function setUrl()
    {
        $this->apiMethod->setUrl("{$this->apiUrl}/IEconItems_730/GetStoreMetaData/v1/");
    }

    public function setParameters()
    {
        $this->apiMethod->setParameters([
            'key' => $this->apiKey,
            'format' => 'json',
            'language' => $this->locale,
        ]);
    }

    public function getApiMethod()
    {
        return $this->apiMethod;
    }
}