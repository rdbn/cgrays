<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:29
 */

namespace DotaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="DotaBundle\Repository\OrderProductDotaRepository")
 * @ORM\Table(name="order_product_dota")
 */
class OrderProductDota
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\ProductPriceDota", inversedBy="orderProductDota")
     * @ORM\JoinColumn(name="product_price_dota_id", referencedColumnName="id")
     */
    protected $productPriceDota;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\User", inversedBy="orderProductDota")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrderProductDota
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
     * Set productPriceDota
     *
     * @param \DotaBundle\Entity\ProductPriceDota $productPriceDota
     *
     * @return OrderProductDota
     */
    public function setProductPriceDota(\DotaBundle\Entity\ProductPriceDota $productPriceDota = null)
    {
        $this->productPriceDota = $productPriceDota;

        return $this;
    }

    /**
     * Get productPriceDota
     *
     * @return \DotaBundle\Entity\ProductPriceDota
     */
    public function getProductPriceDota()
    {
        return $this->productPriceDota;
    }

    /**
     * Set users
     *
     * @param \DotaBundle\Entity\User $users
     *
     * @return OrderProductDota
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
}
