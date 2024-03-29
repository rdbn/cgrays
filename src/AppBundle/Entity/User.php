<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 02.04.17
 * Time: 23:20
 */

namespace AppBundle\Entity;

use AppBundle\Services\UploadImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SteamBundle\Security\User\SteamUserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
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
     * @ORM\Column(name="href_trade", type="text", options={"default": ""})
     */
    protected $hrefTrade;

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
     * @ORM\Column(name="last_online", type="datetime")
     */
    protected $lastOnline;

    /**
     * @ORM\Column(name="is_sell", type="boolean", options={"default": false})
     */
    protected $isSell;

    /**
     * @ORM\Column(name="is_not_check_online", type="boolean", options={"default": false})
     */
    protected $isNotCheckOnline;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SkinsTrade", mappedBy="users")
     */
    protected $skinsTrade;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SkinsPrice", mappedBy="users")
     */
    protected $skinsPrice;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="user")
     */
    protected $payment;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\NewsComment", mappedBy="user")
     */
    protected $newsComment;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsDropUser", mappedBy="user")
     */
    protected $casesSkinsDropUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BalanceUser", mappedBy="user")
     */
    protected $balanceUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesBalanceUser", mappedBy="user")
     */
    protected $casesBalanceUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserPickUpSkinsSteam", mappedBy="user")
     */
    protected $userPickUpSkinsUser;

    /**
     * Unmapped property to handle change password
     */
    private $plainPassword;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    public function __construct()
    {
        $this->isOnline = false;
        $this->isSell = false;
        $this->isNotCheckOnline = false;
        $this->hrefTrade = "";
        $this->salt = md5(uniqid(time(), true));

        $this->createdAt = new \DateTime();
        $this->lastOnline = new \DateTime();

        $this->balanceUser = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->skinsTrade = new ArrayCollection();
        $this->skinsPrice = new ArrayCollection();
        $this->payment = new ArrayCollection();
        $this->newsComment = new ArrayCollection();
        $this->casesSkinsDropUser = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->username;
    }

    public function getRoles()
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getRole();
        }

        return $roles;
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
     * @param $plainPassword
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if ($this->getAvatar()) {
            $filename = __DIR__ . "/../../../web{$this->getAvatar()}";
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        $uploadDir = UploadImageService::UPLOAD_IMAGE_PATH;
        $nameImage = $uploadDir.md5(uniqid(time(), true)).'.png';
        $this->getFile()->move($uploadDir, $nameImage);

        $this->avatar = $nameImage;
        $this->setFile(null);
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hrefTrade
     *
     * @param string $hrefTrade
     *
     * @return User
     */
    public function setHrefTrade($hrefTrade)
    {
        $this->hrefTrade = $hrefTrade;

        return $this;
    }

    /**
     * Get hrefTrade
     *
     * @return int
     */
    public function getHrefTrade()
    {
        return $this->hrefTrade;
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
     * @param \DateTime $createdAt
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set isOnline
     *
     * @param boolean $isOnline
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
     * @return boolean
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * Set lastOnline
     *
     * @param \DateTime $lastOnline
     *
     * @return User
     */
    public function setLastOnline($lastOnline)
    {
        $this->lastOnline = $lastOnline;

        return $this;
    }

    /**
     * Get lastOnline
     *
     * @return \DateTime
     */
    public function getLastOnline()
    {
        return $this->lastOnline;
    }

    /**
     * Set isSell
     *
     * @param boolean $isSell
     *
     * @return User
     */
    public function setIsSell($isSell)
    {
        $this->isSell = $isSell;

        return $this;
    }

    /**
     * Get isSell
     *
     * @return boolean
     */
    public function getIsSell()
    {
        return $this->isSell;
    }

    /**
     * Set isNotCheckOnline
     *
     * @param boolean $isNotCheckOnline
     *
     * @return User
     */
    public function setIsNotCheckOnline($isNotCheckOnline)
    {
        $this->isNotCheckOnline = $isNotCheckOnline;

        return $this;
    }

    /**
     * Get isNotCheckOnline
     *
     * @return boolean
     */
    public function getIsNotCheckOnline()
    {
        return $this->isNotCheckOnline;
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
     * @return ArrayCollection
     */
    public function getRole()
    {
        return $this->roles;
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
     * Add skinsTrade
     *
     * @param \AppBundle\Entity\SkinsTrade $skinsTrade
     *
     * @return User
     */
    public function addSkinsTrade(\AppBundle\Entity\SkinsTrade $skinsTrade)
    {
        $this->skinsTrade[] = $skinsTrade;

        return $this;
    }

    /**
     * Remove skinsTrade
     *
     * @param \AppBundle\Entity\SkinsTrade $skinsTrade
     */
    public function removeSkinsTrade(\AppBundle\Entity\SkinsTrade $skinsTrade)
    {
        $this->skinsTrade->removeElement($skinsTrade);
    }

    /**
     * Get skinsTrade
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkinsTrade()
    {
        return $this->skinsTrade;
    }

    /**
     * Add skinsPrice
     *
     * @param \AppBundle\Entity\SkinsPrice $skinsPrice
     *
     * @return User
     */
    public function addSkinsPrice(\AppBundle\Entity\SkinsPrice $skinsPrice)
    {
        $this->skinsPrice[] = $skinsPrice;

        return $this;
    }

    /**
     * Remove skinsPrice
     *
     * @param \AppBundle\Entity\SkinsPrice $skinsPrice
     */
    public function removeSkinsPrice(\AppBundle\Entity\SkinsPrice $skinsPrice)
    {
        $this->skinsPrice->removeElement($skinsPrice);
    }

    /**
     * Get skinsPrice
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkinsPrice()
    {
        return $this->skinsPrice;
    }

    /**
     * Add payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return User
     */
    public function addPayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payment[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \AppBundle\Entity\Payment $payment
     */
    public function removePayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payment->removeElement($payment);
    }

    /**
     * Get payment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add newsComment
     *
     * @param \AppBundle\Entity\NewsComment $newsComment
     *
     * @return User
     */
    public function addNewsComment(\AppBundle\Entity\NewsComment $newsComment)
    {
        $this->newsComment[] = $newsComment;

        return $this;
    }

    /**
     * Remove newsComment
     *
     * @param \AppBundle\Entity\NewsComment $newsComment
     */
    public function removeNewsComment(\AppBundle\Entity\NewsComment $newsComment)
    {
        $this->newsComment->removeElement($newsComment);
    }

    /**
     * Get newsComment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNewsComment()
    {
        return $this->newsComment;
    }

    /**
     * Add casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser
     *
     * @return User
     */
    public function addCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser)
    {
        $this->casesSkinsDropUser[] = $casesSkinsDropUser;

        return $this;
    }

    /**
     * Remove casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser
     */
    public function removeCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser)
    {
        $this->casesSkinsDropUser->removeElement($casesSkinsDropUser);
    }

    /**
     * Get casesSkinsDropUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesSkinsDropUser()
    {
        return $this->casesSkinsDropUser;
    }

    /**
     * Add balanceUser
     *
     * @param \AppBundle\Entity\BalanceUser $balanceUser
     *
     * @return User
     */
    public function addBalanceUser(\AppBundle\Entity\BalanceUser $balanceUser)
    {
        $this->balanceUser[] = $balanceUser;

        return $this;
    }

    /**
     * Remove balanceUser
     *
     * @param \AppBundle\Entity\BalanceUser $balanceUser
     */
    public function removeBalanceUser(\AppBundle\Entity\BalanceUser $balanceUser)
    {
        $this->balanceUser->removeElement($balanceUser);
    }

    /**
     * Get balanceUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBalanceUser()
    {
        return $this->balanceUser;
    }

    /**
     * Add casesBalanceUser
     *
     * @param \AppBundle\Entity\CasesBalanceUser $casesBalanceUser
     *
     * @return User
     */
    public function addCasesBalanceUser(\AppBundle\Entity\CasesBalanceUser $casesBalanceUser)
    {
        $this->casesBalanceUser[] = $casesBalanceUser;

        return $this;
    }

    /**
     * Remove casesBalanceUser
     *
     * @param \AppBundle\Entity\CasesBalanceUser $casesBalanceUser
     */
    public function removeCasesBalanceUser(\AppBundle\Entity\CasesBalanceUser $casesBalanceUser)
    {
        $this->casesBalanceUser->removeElement($casesBalanceUser);
    }

    /**
     * Get casesBalanceUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesBalanceUser()
    {
        return $this->casesBalanceUser;
    }

    /**
     * Add userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser
     *
     * @return User
     */
    public function addUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser[] = $userPickUpSkinsUser;

        return $this;
    }

    /**
     * Remove userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser
     */
    public function removeUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser->removeElement($userPickUpSkinsUser);
    }

    /**
     * Get userPickUpSkinsUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserPickUpSkinsUser()
    {
        return $this->userPickUpSkinsUser;
    }
}
