<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesSkinsPUBGRepository")
 * @ORM\Table(name="cases_skins_pubg")
 */
class CasesSkinsPUBG
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SkinsPUBG", inversedBy="casesSkins")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cases", inversedBy="casesSkinsPubg")
     * @ORM\JoinColumn(name="cases_id", referencedColumnName="id")
     */
    protected $cases;

    /**
     * @Assert\Range(min="0", max="100")
     * @ORM\Column(name="procent_rarity", type="smallint", options={"default": 0})
     */
    protected $procentRarity;

    /**
     * @Assert\Range(min="0", max="100")
     * @ORM\Column(name="is_drop", type="boolean", options={"default": TRUE})
     */
    protected $isDrop;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->procentRarity = 0;
        $this->isDrop = true;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->procentRarity;
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
     * Set procentRarity
     *
     * @param integer $procentRarity
     *
     * @return CasesSkinsPUBG
     */
    public function setProcentRarity($procentRarity)
    {
        $this->procentRarity = $procentRarity;

        return $this;
    }

    /**
     * Get procentRarity
     *
     * @return integer
     */
    public function getProcentRarity()
    {
        return $this->procentRarity;
    }

    /**
     * Set isDrop
     *
     * @param integer $isDrop
     *
     * @return CasesSkinsPUBG
     */
    public function setIsDrop($isDrop)
    {
        $this->isDrop = $isDrop;

        return $this;
    }

    /**
     * Get isDrop
     *
     * @return integer
     */
    public function getIsDrop()
    {
        return $this->isDrop;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CasesSkinsPUBG
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
     * @param \AppBundle\Entity\SkinsPUBG $skins
     *
     * @return CasesSkinsPUBG
     */
    public function setSkins(\AppBundle\Entity\SkinsPUBG $skins = null)
    {
        $this->skins = $skins;

        return $this;
    }

    /**
     * Get skins
     *
     * @return \AppBundle\Entity\SkinsPUBG
     */
    public function getSkins()
    {
        return $this->skins;
    }

    /**
     * Set cases
     *
     * @param \AppBundle\Entity\Cases $cases
     *
     * @return CasesSkinsPUBG
     */
    public function setCases(\AppBundle\Entity\Cases $cases = null)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get cases
     *
     * @return \AppBundle\Entity\Cases
     */
    public function getCases()
    {
        return $this->cases;
    }
}
