<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.05.17
 * Time: 10:22
 */

namespace SteamBundle\Modal;

use GuzzleHttp\Client;

class ApiMethod
{
    /**
     * @var Client
     */
    private $guzzle;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $parameters;

    /**
     * ApiMethod constructor.
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        $response = $this->guzzle->request('GET', $this->url, ['query' => $this->parameters]);
        $products = json_decode($response->getBody(), true);

        return $products;
    }
}