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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesSkinsDropUserPUBGRepository")
 * @ORM\Table(name="cases_skins_drop_user_pubg")
 */
class CasesSkinsDropUserPUBG
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SkinsPUBG", inversedBy="casesSkinsDropUser")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain", inversedBy="casesSkinsDropUser")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="casesSkinsDropUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * CasesSkinsDropUser constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CasesSkinsDropUserPUBG
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
     * @return CasesSkinsDropUserPUBG
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
     * Set casesDomain
     *
     * @param \AppBundle\Entity\CasesDomain $casesDomain
     *
     * @return CasesSkinsDropUserPUBG
     */
    public function setCasesDomain(\AppBundle\Entity\CasesDomain $casesDomain = null)
    {
        $this->casesDomain = $casesDomain;

        return $this;
    }

    /**
     * Get casesDomain
     *
     * @return \AppBundle\Entity\CasesDomain
     */
    public function getCasesDomain()
    {
        return $this->casesDomain;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return CasesSkinsDropUserPUBG
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
