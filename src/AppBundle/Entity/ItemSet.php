<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 22.08.17
 * Time: 10:52
 */

namespace AppBundle\Entity;

use AppBundle\Modal\DictionaryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_set")
 */
class ItemSet implements DictionaryInterface
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Skins", mappedBy="itemSet")
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
     * @return ItemSet
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
     * @return ItemSet
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
     * Add skin
     *
     * @param \AppBundle\Entity\Skins $skin
     *
     * @return ItemSet
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
