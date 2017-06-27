<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 11:02
 */

namespace SteamBundle\DotaMethod;

use SteamBundle\Modal\AbstractMethod;
use SteamBundle\Modal\ApiMethod;

/**
 * https://api.steampowered.com/IEconDOTA2_570/GetRarities/v1/
 *
 * Class RarenessDotaMethod
 * @package SteamBundle\DotaMethod
 */
class RaritiesBuilderDotaMethod extends AbstractMethod
{
    public function setUrl()
    {
        $this->apiMethod->setUrl("{$this->apiUrl}/IEconDOTA2_570/GetRarities/v1/");
    }

    public function setParameters()
    {
        $this->apiMethod->setParameters([
            'key' => $this->apiKey,
            'format' => 'json',
            'language' => $this->locale
        ]);
    }

    /**
     * @return ApiMethod
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }
}