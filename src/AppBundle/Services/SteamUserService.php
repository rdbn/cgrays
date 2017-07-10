<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;
use SteamBundle\Modal\ApiSteamMethod;

class SteamUserService
{
    /**
     * @var ApiSteamMethod
     */
    private $steamApi;

    /**
     * @var UploadImageService
     */
    private $uploadImage;

    /**
     * SteamUserService constructor.
     * @param ApiSteamMethod  $steamApi
     * @param UploadImageService $uploadImage
     */
    public function __construct(ApiSteamMethod $steamApi, UploadImageService $uploadImage)
    {
        $this->steamApi = $steamApi;
        $this->uploadImage = $uploadImage;
    }

    /**
     * @param $steamId
     * @return mixed
     */
    public function getUserData($steamId)
    {
        $userData = $this->steamApi->setSteamId($steamId)->getResult();
        if (isset($userData['response']['players'][0])) {
            return $userData['response']['players'][0];
        }
        return NULL;
    }

    /**
     * @param User $user
     * @return User
     */
    public function updateUserEntry(User $user)
    {
        $userData = $this->getUserData($user->getSteamId());
        $avatar = $this->uploadImage->upload($userData['avatarfull'], false);

        $user->setUsername($userData['personaname']);
        $user->setAvatar(UploadImageService::UPLOAD_IMAGE_PATH.$avatar);
        return $user;
    }
}
