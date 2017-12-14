<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use AppBundle\Services\GenerateUUID;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cases_domain")
 */
class CasesDomain
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $domain;

    /**
     * @ORM\Column(type="string", length=36)
     */
    protected $uuid;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cases", mappedBy="casesDomain")
     */
    protected $cases;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesStaticPage", mappedBy="casesDomain")
     */
    protected $casesStaticPage;

    public function __construct()
    {
        $this->uuid = GenerateUUID::getUUID();
        $this->createdAt = new \DateTime();
        $this->cases = new ArrayCollection();
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
     * Set domain
     *
     * @param string $domain
     *
     * @return CasesDomain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return CasesDomain
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CasesDomain
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
     * Add case
     *
     * @param \AppBundle\Entity\Cases $case
     *
     * @return CasesDomain
     */
    public function addCase(\AppBundle\Entity\Cases $case)
    {
        $this->cases[] = $case;

        return $this;
    }

    /**
     * Remove case
     *
     * @param \AppBundle\Entity\Cases $case
     */
    public function removeCase(\AppBundle\Entity\Cases $case)
    {
        $this->cases->removeElement($case);
    }

    /**
     * Get cases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCases()
    {
        return $this->cases;
    }
}
