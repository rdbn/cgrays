<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 11:00
 */

namespace SteamBundle\DotaMethod;

use SteamBundle\Modal\AbstractMethod;

/**
 * https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v1/
 *
 * Class HeroesDotaMethod
 * @package SteamBundle\DotaMethod
 */
class HeroesBuilderDotaMethod extends AbstractMethod
{
    public function setUrl()
    {
        $this->apiMethod->setUrl("{$this->apiUrl}/IEconDOTA2_570/GetHeroes/v1/");
    }

    public function setParameters()
    {
        $this->apiMethod->setParameters([
            'key' => $this->apiKey,
            'format' => 'json',
            'language' => $this->locale,
            'itemizedonly' => 1
        ]);
    }

    public function getApiMethod()
    {
        return $this->apiMethod;
    }
}