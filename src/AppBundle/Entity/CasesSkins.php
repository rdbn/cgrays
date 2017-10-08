<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="smallint")
     */
    protected $count;

    /**
     * @ORM\Column(name="count_drop", type="smallint", options={"default": 0})
     */
    protected $countDrop;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->countDrop = 0;
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->countDrop;
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
     * Set count
     *
     * @param string $count
     *
     * @return CasesSkins
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return string
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set countDrop
     *
     * @param string $countDrop
     *
     * @return CasesSkins
     */
    public function setCountDrop($countDrop)
    {
        $this->countDrop = $countDrop;

        return $this;
    }

    /**
     * Get countDrop
     *
     * @return string
     */
    public function getCountDrop()
    {
        return $this->countDrop;
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
