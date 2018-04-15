<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 09.10.17
 * Time: 1:37
 */

namespace SteamBundle\SteamMethod;

use GuzzleHttp\Client;

class Market
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
     * @param $startId
     * @param $gameId
     * @param $count
     * @return array
     */
    public function getResult($startId, $gameId, $count)
    {
        $url = "{$this->steamCommunityUrl}/market/search/render/?";
        $parameters = [
            'query' => "appid:{$gameId}",
            'start' => $startId,
            'count' => $count,
            'search_descriptions' => 0,
            'sort_column' => 'popular',
            'sort_dir' => 'desc'
        ];

        $response = $this->guzzle->request('GET', $url, ['query' => $parameters]);
        $products = json_decode($response->getBody(), true);

        return $products;
    }
}