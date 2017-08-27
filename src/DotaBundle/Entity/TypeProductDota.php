<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace DotaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="type_product_dota")
 */
class TypeProductDota
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="DotaBundle\Entity\ProductDota", mappedBy="typeProductDota")
     */
    protected $productsDota;

    public function __construct()
    {
        $this->productsDota = new ArrayCollection();
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
     * @return TypeProductDota
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
     * Set title
     *
     * @param string $title
     *
     * @return TypeProductDota
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add productsDotum
     *
     * @param \DotaBundle\Entity\ProductDota $productsDotum
     *
     * @return TypeProductDota
     */
    public function addProductsDotum(\DotaBundle\Entity\ProductDota $productsDotum)
    {
        $this->productsDota[] = $productsDotum;

        return $this;
    }

    /**
     * Remove productsDotum
     *
     * @param \DotaBundle\Entity\ProductDota $productsDotum
     */
    public function removeProductsDotum(\DotaBundle\Entity\ProductDota $productsDotum)
    {
        $this->productsDota->removeElement($productsDotum);
    }

    /**
     * Get productsDota
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsDota()
    {
        return $this->productsDota;
    }
}
