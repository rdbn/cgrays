<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 0:39
 */

namespace ApiBundle\Service;

use Predis\Client;

class GamesService
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * GenerateIdGamesService constructor.
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param $userId
     * @return string
     */
    public function getGame($userId)
    {
        return $this->redis->get("games_{$userId}");
    }

    /**
     * @param $userId
     * @param $skinsId
     */
    public function flushRedisGame($userId, $skinsId)
    {
        $this->redis->set("games_{$userId}", $skinsId);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function checkGameUserId($userId)
    {
        $gamesUserId = $this->redis->get("games_{$userId}");
        if ($gamesUserId) {
            return true;
        }

        return false;
    }

    /**
     * @param $userId
     */
    public function clearGame($userId)
    {
        $this->redis->del(["games_{$userId}"]);
    }
}