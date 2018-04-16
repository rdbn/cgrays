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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RarityPUBGRepository")
 * @ORM\Table(name="rarity_pubg")
 */
class RarityPUBG implements DictionaryInterface
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SkinsPUBG", mappedBy="rarity")
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
        return (string) $this->localizedTagName;
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
     * Set localizedTagName
     *
     * @param string $localizedTagName
     *
     * @return RarityPUBG
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
     * @param \AppBundle\Entity\SkinsPUBG $skin
     *
     * @return RarityPUBG
     */
    public function addSkin(\AppBundle\Entity\SkinsPUBG $skin)
    {
        $this->skins[] = $skin;

        return $this;
    }

    /**
     * Remove skin
     *
     * @param \AppBundle\Entity\SkinsPUBG $skin
     */
    public function removeSkin(\AppBundle\Entity\SkinsPUBG $skin)
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
