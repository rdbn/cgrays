<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:28
 */

namespace DotaBundle\Entity;

use DotaBundle\DotaBundle;
use DotaBundle\Services\UploadImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="DotaBundle\Repository\ProductDotaRepository")
 * @ORM\Table(name="product_dota")
 */
class ProductDota
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\HeroesDota", inversedBy="productsDota")
     * @ORM\JoinColumn(name="heroes_dota_id", referencedColumnName="id")
     */
    protected $heroesDota;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\TypeProductDota", inversedBy="productsDota")
     * @ORM\JoinColumn(name="type_product_dota_id", referencedColumnName="id")
     */
    protected $typeProductDota;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\QualityDota", inversedBy="productsDota")
     * @ORM\JoinColumn(name="quality_dota_id", referencedColumnName="id")
     */
    protected $qualityDota;

    /**
     * @ORM\ManyToOne(targetEntity="DotaBundle\Entity\RarityDota", inversedBy="productsDota")
     * @ORM\JoinColumn(name="rarity_dota_id", referencedColumnName="id")
     */
    protected $rarityDota;

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
     * @ORM\OneToMany(targetEntity="DotaBundle\Entity\ProductPriceDota", mappedBy="productDota")
     */
    protected $productPriceDota;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->productPriceDota = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return ProductDota
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
     * @return ProductDota
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;

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
     * @return ProductDota
     */
    public function setIconUrlLarge($iconUrlLarge)
    {
        $this->iconUrlLarge = $iconUrlLarge;

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
     * @return ProductDota
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
     * @return ProductDota
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
     * Set heroesDota
     *
     * @param \DotaBundle\Entity\HeroesDota $heroesDota
     *
     * @return ProductDota
     */
    public function setHeroesDota(\DotaBundle\Entity\HeroesDota $heroesDota = null)
    {
        $this->heroesDota = $heroesDota;

        return $this;
    }

    /**
     * Get heroesDota
     *
     * @return \DotaBundle\Entity\HeroesDota
     */
    public function getHeroesDota()
    {
        return $this->heroesDota;
    }

    /**
     * Set typeProductDota
     *
     * @param \DotaBundle\Entity\TypeProductDota $typeProductDota
     *
     * @return ProductDota
     */
    public function setTypeProductDota(\DotaBundle\Entity\TypeProductDota $typeProductDota = null)
    {
        $this->typeProductDota = $typeProductDota;

        return $this;
    }

    /**
     * Get typeProductDota
     *
     * @return \DotaBundle\Entity\TypeProductDota
     */
    public function getTypeProductDota()
    {
        return $this->typeProductDota;
    }

    /**
     * Set qualityDota
     *
     * @param \DotaBundle\Entity\QualityDota $qualityDota
     *
     * @return ProductDota
     */
    public function setQualityDota(\DotaBundle\Entity\QualityDota $qualityDota = null)
    {
        $this->qualityDota = $qualityDota;

        return $this;
    }

    /**
     * Get qualityDota
     *
     * @return \DotaBundle\Entity\QualityDota
     */
    public function getQualityDota()
    {
        return $this->qualityDota;
    }

    /**
     * Set rarityDota
     *
     * @param \DotaBundle\Entity\RarityDota $rarityDota
     *
     * @return ProductDota
     */
    public function setRarityDota(\DotaBundle\Entity\RarityDota $rarityDota = null)
    {
        $this->rarityDota = $rarityDota;

        return $this;
    }

    /**
     * Get rarityDota
     *
     * @return \DotaBundle\Entity\RarityDota
     */
    public function getRarityDota()
    {
        return $this->rarityDota;
    }

    /**
     * Add productPriceDotum
     *
     * @param \DotaBundle\Entity\ProductPriceDota $productPriceDotum
     *
     * @return ProductDota
     */
    public function addProductPriceDotum(\DotaBundle\Entity\ProductPriceDota $productPriceDotum)
    {
        $this->productPriceDota[] = $productPriceDotum;

        return $this;
    }

    /**
     * Remove productPriceDotum
     *
     * @param \DotaBundle\Entity\ProductPriceDota $productPriceDotum
     */
    public function removeProductPriceDotum(\DotaBundle\Entity\ProductPriceDota $productPriceDotum)
    {
        $this->productPriceDota->removeElement($productPriceDotum);
    }

    /**
     * Get productPriceDota
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductPriceDota()
    {
        return $this->productPriceDota;
    }
}
