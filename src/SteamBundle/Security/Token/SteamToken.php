<?php

namespace SteamBundle\Security\Token;

use SteamBundle\Security\User\SteamUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;

class SteamToken implements TokenInterface, SteamUserInterface
{
    private $user;
    private $username;
    private $steamId;
    private $avatar;
    private $authenticated;
    private $attributes = [];
    private $roles;

    public function __toString()
    {
        return $this->getSteamId();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole($role)
    {
        if ($role instanceof RoleInterface) {
            $this->roles[] = $role;
        } else {
            $this->roles[] = new Role($role);
        }
        return $this;
    }

    public function getCredentials()
    {
        return '';
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->roles = [];
        foreach ($user->getRoles() as $role) {
            $this->roles[] = new Role($role);
        }

        $this->user = $user;

        return $this;
    }

    public function getUsername()
    {
        return $this->user ? $this->user->getUsername() : $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function setSteamId($steamId)
    {
        $this->steamId = $steamId;

        return $this;
    }

    public function getSteamId()
    {
        return $this->user ? $this->user->getSteamId() : $this->steamId;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->user ? $this->user->getAvatar() : $this->steamId;
    }

    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    public function setAuthenticated($authenticated)
    {
        $this->authenticated = $authenticated;
        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $key => $attribute) {
            $key = str_replace("openid_", "openid.", $key);
            $this->attributes[$key] = $attribute;
        }
        return $this;
    }

    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function serialize()
    {
        return serialize([
            'attributes' => $this->attributes,
            'authenticated' => $this->isAuthenticated(),
            'steamId' => $this->getSteamId(),
            'roles' => $this->getRoles(),
            'user' => $this->getUser(),
        ]);
    }

    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->attributes = $data['attributes'];
        $this->setAuthenticated($data['authenticated']);
        $this->setSteamId($data['steamId']);
        $this->setUser($data['user']);
        $this->roles = $data['roles'];
    }
}
