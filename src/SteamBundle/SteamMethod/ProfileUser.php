<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 11:00
 */

namespace SteamBundle\SteamMethod;

use SteamBundle\Modal\AbstractMethod;

/**
 * https://api.steampowered.com/GetPlayerSummaries/v0002/
 *
 * Class ProfileUserMethod
 * @package SteamBundle\SteamMethod
 */
class ProfileUser extends AbstractMethod
{
    public function setUrl()
    {
        $this->apiMethod->setUrl("{$this->apiUrl}/ISteamUser/GetPlayerSummaries/v2/");
    }

    public function setParameters()
    {
        $this->apiMethod->setParameters([
            'key' => $this->apiKey
        ]);
    }

    public function getApiMethod()
    {
        return $this->apiMethod;
    }
}