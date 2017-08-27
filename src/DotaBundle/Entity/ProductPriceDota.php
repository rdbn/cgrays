<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:29
 */

namespace DotaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DotaBundle\Repository\ProductPriceDotaRepository")
 * @ORM\Table(name="product_price_dota")
 */
class ProductPriceDota
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="class_id", type="string", length=255, unique=true)
     */
    protected $classId;

    /**
     * @ORM\Column(name="instance_id", type="string", length=255, unique=true)
     */
    protected $instanceId;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\ProductDota", inversedBy="productPriceDota")
     * @ORM\JoinColumn(name="product_dota_id", referencedColumnName="id")
     */
    protected $productDota;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\User", inversedBy="productPriceDota")
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
     * @ORM\Column(type="datetime", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="DotaBundle\Entity\OrderProductDota", mappedBy="productPriceDota")
     */
    protected $orderProductDota;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orderProductDota = new ArrayCollection();
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
     * @return ProductPriceDota
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
     * @return ProductPriceDota
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
     * Set price
     *
     * @param string $price
     *
     * @return ProductPriceDota
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
     * @return ProductPriceDota
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
     * @return ProductPriceDota
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
     * @return ProductPriceDota
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
     * Set productDota
     *
     * @param \DotaBundle\Entity\ProductDota $productDota
     *
     * @return ProductPriceDota
     */
    public function setProductDota(\DotaBundle\Entity\ProductDota $productDota = null)
    {
        $this->productDota = $productDota;

        return $this;
    }

    /**
     * Get productDota
     *
     * @return \DotaBundle\Entity\ProductDota
     */
    public function getProductDota()
    {
        return $this->productDota;
    }

    /**
     * Set users
     *
     * @param \DotaBundle\Entity\User $users
     *
     * @return ProductPriceDota
     */
    public function setUsers(\DotaBundle\Entity\User $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \DotaBundle\Entity\User
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add orderProductDotum
     *
     * @param \DotaBundle\Entity\OrderProductDota $orderProductDotum
     *
     * @return ProductPriceDota
     */
    public function addOrderProductDotum(\DotaBundle\Entity\OrderProductDota $orderProductDotum)
    {
        $this->orderProductDota[] = $orderProductDotum;

        return $this;
    }

    /**
     * Remove orderProductDotum
     *
     * @param \DotaBundle\Entity\OrderProductDota $orderProductDotum
     */
    public function removeOrderProductDotum(\DotaBundle\Entity\OrderProductDota $orderProductDotum)
    {
        $this->orderProductDota->removeElement($orderProductDotum);
    }

    /**
     * Get orderProductDota
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderProductDota()
    {
        return $this->orderProductDota;
    }
}
