<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:29
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkinsTradeRepository")
 * @ORM\Table(name="skins_trade")
 */
class SkinsTrade
{
    const SKINS_BUY = 'buy';
    const SKINS_BEGIN_TRADE = 'begin_trade';
    const SKINS_BEGIN_COMMIT = 'commit_trade';
    const SKINS_BEGIN_ROLLBACK = 'rollback_trade';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SkinsPrice", inversedBy="skinsTrade")
     * @ORM\JoinColumn(name="skins_price_id", referencedColumnName="id")
     */
    protected $skinsPrice;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="skinsTrade")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = self::SKINS_BUY;
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
     * Set status
     *
     * @param string $status
     *
     * @return SkinsTrade
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SkinsTrade
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
     * Set skinsPrice
     *
     * @param \AppBundle\Entity\SkinsPrice $skinsPrice
     *
     * @return SkinsTrade
     */
    public function setSkinsPrice(\AppBundle\Entity\SkinsPrice $skinsPrice = null)
    {
        $this->skinsPrice = $skinsPrice;

        return $this;
    }

    /**
     * Get skinsPrice
     *
     * @return \AppBundle\Entity\SkinsPrice
     */
    public function getSkinsPrice()
    {
        return $this->skinsPrice;
    }

    /**
     * Set users
     *
     * @param \AppBundle\Entity\User $users
     *
     * @return SkinsTrade
     */
    public function setUsers(\AppBundle\Entity\User $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \AppBundle\Entity\User
     */
    public function getUsers()
    {
        return $this->users;
    }
}
