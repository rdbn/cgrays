<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use AppBundle\Modal\DictionaryInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rarity")
 */
class Rarity implements DictionaryInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="internal_name", type="string", length=45)
     */
    protected $internalName;

    /**
     * @ORM\Column(name="localized_tag_name", type="string", length=45)
     */
    protected $localizedTagName;

    /**
     * @ORM\Column(name="color", type="string", length=45)
     */
    protected $color;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Skins", mappedBy="rarity")
     */
    protected $skins;

    public function __construct()
    {
        $this->skins= new ArrayCollection();
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
     * Set internalName
     *
     * @param string $internalName
     *
     * @return Rarity
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * Get internalName
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Set localizedTagName
     *
     * @param string $localizedTagName
     *
     * @return Rarity
     */
    public function setLocalizedTagName($localizedTagName)
    {
        $this->localizedTagName = $localizedTagName;

        return $this;
    }

    /**
     * Get localizedTagName
     *
     * @return string
     */
    public function getLocalizedTagName()
    {
        return $this->localizedTagName;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Rarity
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Add skin
     *
     * @param \AppBundle\Entity\Skins $skin
     *
     * @return Rarity
     */
    public function addSkin(\AppBundle\Entity\Skins $skin)
    {
        $this->skins[] = $skin;

        return $this;
    }

    /**
     * Remove skin
     *
     * @param \AppBundle\Entity\Skins $skin
     */
    public function removeSkin(\AppBundle\Entity\Skins $skin)
    {
        $this->skins->removeElement($skin);
    }

    /**
     * Get skins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkins()
    {
        return $this->skins;
    }
}
