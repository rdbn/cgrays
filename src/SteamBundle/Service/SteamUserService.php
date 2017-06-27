<?php
namespace SteamBundle\Service;

use AppBundle\Entity\User;
use GuzzleHttp\Client;

class SteamUserService
{
    private $guzzle;
    private $steamKey;

    public function __construct(Client $guzzle, $steamKey)
    {
        $this->guzzle = $guzzle;
        $this->steamKey = $steamKey;
    }

    public function getUserData($communityId)
    {
        $response = $this->guzzle->request('GET', 'GetPlayerSummaries/v0002/', ['query' => ['key' => $this->steamKey, 'steamids' => $communityId]]);
        $userdata = json_decode($response->getBody(), true);
        if (isset($userdata['response']['players'][0])) {
            return $userdata['response']['players'][0];
        }
        return NULL;
    }

    public function updateUserEntry(User $user)
    {
        $userData = $this->getUserData($user->getSteamId());
        $user->setUsername($userData['personaname']);
        $user->setAvatar($userData['avatarfull']);
        return $user;
    }
}
