<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:28
 */

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use AppBundle\Services\UploadImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="\AppBundle\Repository\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Heroes", inversedBy="products")
     * @ORM\JoinColumn(name="heroes_id", referencedColumnName="id")
     */
    protected $heroes;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeProduct", inversedBy="products")
     * @ORM\JoinColumn(name="type_product_id", referencedColumnName="id")
     */
    protected $typeProduct;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Quality", inversedBy="products")
     * @ORM\JoinColumn(name="quality_id", referencedColumnName="id")
     */
    protected $quality;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rarity", inversedBy="products")
     * @ORM\JoinColumn(name="rarity_id", referencedColumnName="id")
     */
    protected $rarity;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="icon_url", type="string", length=255)
     */
    protected $iconUrl;

    /**
     * @ORM\Column(name="icon_url_large", type="string", length=255)
     */
    protected $iconUrlLarge;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Order", mappedBy="product")
     */
    protected $order;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductPrice", mappedBy="product")
     */
    protected $productPrice;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        $this->order = new ArrayCollection();
        $this->productPrice = new ArrayCollection();
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
     * @return Product
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
     * @return Product
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set iconUrl
     *
     * @param string $iconUrl
     *
     * @return Product
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = UploadImageService::UPLOAD_IMAGE_PATH.$iconUrl;

        return $this;
    }

    /**
     * Get iconUrl
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * Set iconUrlLarge
     *
     * @param string $iconUrlLarge
     *
     * @return Product
     */
    public function setIconUrlLarge($iconUrlLarge)
    {
        $this->iconUrlLarge = UploadImageService::UPLOAD_IMAGE_PATH.$iconUrlLarge;

        return $this;
    }

    /**
     * Get iconUrlLarge
     *
     * @return string
     */
    public function getIconUrlLarge()
    {
        return $this->iconUrlLarge;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Product
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
     * Set heroes
     *
     * @param \AppBundle\Entity\Heroes $heroes
     *
     * @return Product
     */
    public function setHeroes(\AppBundle\Entity\Heroes $heroes = null)
    {
        $this->heroes = $heroes;

        return $this;
    }

    /**
     * Get heroes
     *
     * @return \AppBundle\Entity\Heroes
     */
    public function getHeroes()
    {
        return $this->heroes;
    }

    /**
     * Set typeProduct
     *
     * @param \AppBundle\Entity\TypeProduct $typeProduct
     *
     * @return Product
     */
    public function setTypeProduct(\AppBundle\Entity\TypeProduct $typeProduct = null)
    {
        $this->typeProduct = $typeProduct;

        return $this;
    }

    /**
     * Get typeProduct
     *
     * @return \AppBundle\Entity\TypeProduct
     */
    public function getTypeProduct()
    {
        return $this->typeProduct;
    }

    /**
     * Set quality
     *
     * @param \AppBundle\Entity\Quality $quality
     *
     * @return Product
     */
    public function setQuality(\AppBundle\Entity\Quality $quality = null)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return \AppBundle\Entity\Quality
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set rarity
     *
     * @param \AppBundle\Entity\Rarity $rarity
     *
     * @return Product
     */
    public function setRarity(\AppBundle\Entity\Rarity $rarity = null)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return \AppBundle\Entity\Rarity
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return Product
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
     * @param \AppBundle\Entity\ProductPrice $productPrice
     *
     * @return Product
     */
    public function addProductPrice(\AppBundle\Entity\ProductPrice $productPrice)
    {
        $this->productPrice[] = $productPrice;

        return $this;
    }

    /**
     * Remove productPrice
     *
     * @param \AppBundle\Entity\ProductPrice $productPrice
     */
    public function removeProductPrice(\AppBundle\Entity\ProductPrice $productPrice)
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
