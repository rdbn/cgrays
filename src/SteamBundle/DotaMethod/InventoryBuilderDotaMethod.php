<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 10:46
 */

namespace SteamBundle\DotaMethod;

use AppBundle\Entity\User;
use Predis\Client as Redis;
use SteamBundle\Modal\ApiMethod;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * http://steamcommunity.com/inventory/XXXXXXXXXX/570/2?l=english&count=50
 *
 * Class InventoryMethod
 * @package SteamBundle\Method
 */
class InventoryBuilderDotaMethod
{
    /**
     * @var ApiMethod
     */
    protected $apiMethod;

    /**
     * @var Redis
     */
    protected $redis;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $steamCommunityUrl;

    /**
     * @var int
     */
    protected $steamCountItem;

    /**
     * InventoryBuilderDotaMethod constructor.
     * @param ApiMethod $apiMethod
     * @param TokenStorageInterface $token
     * @param $steamCommunityUrl
     * @param $steamCountItem
     */
    public function __construct(ApiMethod $apiMethod, TokenStorageInterface $token, $steamCommunityUrl, $steamCountItem)
    {
        $this->apiMethod = $apiMethod;
        $this->user = $token->getToken()->getUser();
        $this->steamCommunityUrl = $steamCommunityUrl;
        $this->steamCountItem = $steamCountItem;
    }

    /**
     * @param $startId
     * @return array
     */
    public function getResult($startId = null)
    {
        $this->apiMethod->setUrl("{$this->steamCommunityUrl}/inventory/{$this->user->getSteamId()}/570/2?");
        $parameters = ['l' => 'russian', 'count' => $this->steamCountItem];
        if ($startId) {
            $parameters['start_assetid'] = $startId;
        }

        $this->apiMethod->setParameters($parameters);

         return $this->apiMethod->getResult();
    }
}