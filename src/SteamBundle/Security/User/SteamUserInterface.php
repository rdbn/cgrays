<?php

namespace SteamBundle\Security\User;

interface SteamUserInterface
{
    /**
     * Sets the steamId.
     *
     * The steam represents the unique SteamID.
     *
     * @param int $steamId
     */
    public function setSteamId($steamId);

    /**
     * Returns the steamId of the user.
     *
     * @return int steamId
     */
    public function getSteamId();

    /**
     * Returns the url to the avatar of the user.
     *
     * @return string The avatar url
     */
    public function getAvatar();

    /**
     * Sets the url to the users avatar
     *
     * @param string $avatar
     */
    public function setAvatar($avatar);
}
