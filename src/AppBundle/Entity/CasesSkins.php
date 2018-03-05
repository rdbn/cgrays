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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesSkinsRepository")
 * @ORM\Table(name="cases_skins")
 */
class CasesSkins
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skins", inversedBy="casesSkins")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cases", inversedBy="casesSkins")
     * @ORM\JoinColumn(name="cases_id", referencedColumnName="id")
     */
    protected $cases;

    /**
     * @Assert\Range(min="0", max="100")
     * @ORM\Column(name="procent_rarity", type="smallint")
     */
    protected $procentRarity;

    /**
     * @ORM\Column(name="procent_skins", type="boolean", options={"default": TRUE})
     */
    protected $procentSkins;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->procentRarity = 0;
        $this->procentSkins = true;
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->procentRarity;
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
     * @param string $procentRarity
     *
     * @return CasesSkins
     */
    public function setProcentRarity($procentRarity)
    {
        $this->procentRarity = $procentRarity;

        return $this;
    }

    /**
     * Get procentRarity
     *
     * @return string
     */
    public function getProcentRarity()
    {
        return $this->procentRarity;
    }

    /**
     * Set procentSkins
     *
     * @param string $procentSkins
     *
     * @return CasesSkins
     */
    public function setProcentSkins($procentSkins)
    {
        $this->procentSkins = $procentSkins;

        return $this;
    }

    /**
     * Get procentSkins
     *
     * @return string
     */
    public function getProcentSkins()
    {
        return $this->procentSkins;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CasesSkins
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
     * @param \AppBundle\Entity\Skins $skins
     *
     * @return CasesSkins
     */
    public function setSkins(\AppBundle\Entity\Skins $skins = null)
    {
        $this->skins = $skins;

        return $this;
    }

    /**
     * Get skins
     *
     * @return \AppBundle\Entity\Skins
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
     * @return CasesSkins
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
