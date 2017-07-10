<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.05.17
 * Time: 10:22
 */

namespace SteamBundle\Modal;

class ApiSteamMethod extends ApiMethod
{
    /**
     * @param int $steamId
     * @return self
     */
    public function setSteamId($steamId)
    {
        $this->parameters['steamIds'] = $steamId;

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getResult()
    {
        if (!isset($this->parameters)) {
            throw new \Exception("Not found steam api key.");
        }

        return parent::getResult();
    }
}