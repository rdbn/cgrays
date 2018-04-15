<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 10:46
 */

namespace SteamBundle\SteamMethod;

use GuzzleHttp\Client;

/**
 * http://steamcommunity.com/inventory/XXXXXXXXXX/730/2?l=english&count=50
 *
 * Class InventoryMethod
 * @package SteamBundle\Method
 */
class Inventory
{
    /**
     * @var Client
     */
    protected $guzzle;

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
     * @param $steamCommunityUrl
     * @param $steamCountItem
     */
    public function __construct($steamCommunityUrl, $steamCountItem)
    {
        $this->guzzle = new Client();
        $this->steamCommunityUrl = $steamCommunityUrl;
        $this->steamCountItem = $steamCountItem;
    }

    /**
     * @param $steamId
     * @param $startId
     * @param $gameId
     * @return array
     */
    public function getResult($steamId, $startId, $gameId)
    {
        $url = "{$this->steamCommunityUrl}/inventory/{$steamId}/{$gameId}/2?";
        $parameters = ['l' => 'russian', 'count' => $this->steamCountItem];
        if ($startId) {
            $parameters['start_assetid'] = $startId;
        }

        $response = $this->guzzle->request('GET', $url, ['query' => $parameters]);
        $products = json_decode($response->getBody(), true);

        return $products;
    }
}