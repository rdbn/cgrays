<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 02.04.17
 * Time: 23:20
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SteamBundle\Security\User\SteamUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface, EquatableInterface, \Serializable, SteamUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="steam_id", type="string", length=128, unique=true)
     */
    protected $steamId;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    protected $balance;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    protected $roles;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="is_online", type="boolean", options={"default": false})
     */
    protected $isOnline;

    /**
     * @ORM\Column(name="is_sell", type="boolean", options={"default": false})
     */
    protected $isSell;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Order", mappedBy="users")
     */
    protected $order;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductPrice", mappedBy="users")
     */
    protected $productPrice;

    public function __construct()
    {
        $this->balance = 0;
        $this->isOnline = false;
        $this->isSell= false;
        $this->salt = md5(uniqid(time(), true));

        $this->createdAt = new \DateTime();

        $this->roles = new ArrayCollection();
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        return $this->password;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->steamId,
            $this->roles,
        ]);
    }
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->steamId,
            $this->roles,
            ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->getId() === $user->getId()) {
            return true;
        }
        return false;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set steamId
     *
     * @param int $steamId
     *
     * @return User
     */
    public function setSteamId($steamId)
    {
        $this->steamId = $steamId;

        return $this;
    }

    /**
     * Get steamId
     *
     * @return int
     */
    public function getSteamId()
    {
        return $this->steamId;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isOnline
     *
     * @param bool $isOnline
     *
     * @return User
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return bool
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Set isSell
     *
     * @param bool $isSell
     *
     * @return User
     */
    public function setIsSell($isSell)
    {
        $this->isSell = $isSell;

        return $this;
    }

    /**
     * Get isOnline
     *
     * @return bool
     */
    public function getIsSell()
    {
        return $this->isSell;
    }

    /**
     * Add role
     *
     * @param \AppBundle\Entity\Role $role
     *
     * @return User
     */
    public function addRole(\AppBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \AppBundle\Entity\Role $role
     */
    public function removeRole(\AppBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return User
     */
    public function addOrder(\AppBundle\Entity\Order $order)
    {
        $this->order[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Order $order
     */
    public function removeOrder(\AppBundle\Entity\Order $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Get order
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add productPrice
     *
     * @param \AppBundle\Entity\Product $productPrice
     *
     * @return User
     */
    public function addProductPrice(\AppBundle\Entity\Product $productPrice)
    {
        $this->productPrice[] = $productPrice;

        return $this;
    }

    /**
     * Remove productPrice
     *
     * @param \AppBundle\Entity\Product $productPrice
     */
    public function removeProductPrice(\AppBundle\Entity\Product $productPrice)
    {
        $this->productPrice->removeElement($productPrice);
    }

    /**
     * Get productPrice
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }
}
