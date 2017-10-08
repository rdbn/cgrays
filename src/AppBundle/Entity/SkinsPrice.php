<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:29
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkinsPriceRepository")
 * @ORM\Table(name="skins_price")
 */
class SkinsPrice
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="class_id", type="integer")
     */
    protected $classId;

    /**
     * @ORM\Column(name="instance_id", type="integer")
     */
    protected $instanceId;

    /**
     * @ORM\Column(name="asset_id", type="integer")
     */
    protected $assetId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skins", inversedBy="skinsPrice")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="skinsPrice")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(name="is_sell", type="boolean", options={"default": TRUE})
     */
    protected $isSell;

    /**
     * @ORM\Column(name="is_remove", type="boolean", options={"default": FALSE})
     */
    protected $isRemove;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SkinsTrade", mappedBy="skinsPrice")
     */
    protected $skinsTrade;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->skinsTrade = new ArrayCollection();
        $this->isSell = true;
        $this->isRemove = false;
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
     * Set classId
     *
     * @param string $classId
     *
     * @return SkinsPrice
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;

        return $this;
    }

    /**
     * Get classId
     *
     * @return string
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * Set instanceId
     *
     * @param string $instanceId
     *
     * @return SkinsPrice
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * Get instanceId
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * Set assetId
     *
     * @param string $assetId
     *
     * @return SkinsPrice
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;

        return $this;
    }

    /**
     * Get assetId
     *
     * @return string
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return SkinsPrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isSell
     *
     * @param boolean $isSell
     *
     * @return SkinsPrice
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
     * Set isRemove
     *
     * @param boolean $isRemove
     *
     * @return SkinsPrice
     */
    public function setIsRemove($isRemove)
    {
        $this->isRemove = $isRemove;

        return $this;
    }

    /**
     * Get isRemove
     *
     * @return boolean
     */
    public function getIsRemove()
    {
        return $this->isRemove;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SkinsPrice
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
     * Set skins
     *
     * @param \AppBundle\Entity\Skins $skins
     *
     * @return SkinsPrice
     */
    public function setSkins(\AppBundle\Entity\Skins $skins = null)
    {
        $this->skins = $skins;

        return $this;
    }

    /**
     * Get skins
     *
     * @return \AppBundle\Entity\Skins
     */
    public function getSkins()
    {
        return $this->skins;
    }

    /**
     * Set users
     *
     * @param \AppBundle\Entity\User $users
     *
     * @return SkinsPrice
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

    /**
     * Add skinsTrade
     *
     * @param \AppBundle\Entity\SkinsTrade $orderSkin
     *
     * @return SkinsPrice
     */
    public function addSkinsTrade(\AppBundle\Entity\SkinsTrade $orderSkin)
    {
        $this->skinsTrade[] = $orderSkin;

        return $this;
    }

    /**
     * Remove skinsTrade
     *
     * @param \AppBundle\Entity\SkinsTrade $orderSkin
     */
    public function removeSkinsTrade(\AppBundle\Entity\SkinsTrade $orderSkin)
    {
        $this->skinsTrade->removeElement($orderSkin);
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
}
