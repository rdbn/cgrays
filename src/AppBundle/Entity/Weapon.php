<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 22.08.17
 * Time: 10:53
 */

namespace AppBundle\Entity;

use AppBundle\Modal\DictionaryInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="weapon")
 */
class Weapon implements DictionaryInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="localized_tag_name", type="string", length=45)
     */
    protected $localizedTagName;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Skins", mappedBy="weapon")
     */
    protected $skins;

    public function __construct()
    {
        $this->skins= new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->localizedTagName;
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
     * @return Weapon
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
     * @return Weapon
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
     * @return Weapon
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
