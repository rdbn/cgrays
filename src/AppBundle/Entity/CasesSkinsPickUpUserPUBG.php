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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesSkinsPickUpUserPUBGRepository")
 * @ORM\Table(name="cases_skins_pick_up_user_pubg")
 */
class CasesSkinsPickUpUserPUBG
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SkinsPUBG", inversedBy="casesSkinsPickUpUser")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain", inversedBy="casesSkinsPickUpUser")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="casesSkinsDropUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="is_remove", type="boolean", options={"default": FALSE})
     */
    protected $isRemove;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->isRemove = false;
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
     * Set isRemove
     *
     * @param boolean $isRemove
     *
     * @return CasesSkinsPickUpUserPUBG
     */
    public function setIsRemove($isRemove)
    {
        $this->isRemove = $isRemove;

        return $this;
    }

    /**
     * Get isRemove
     *
     * @return boolean
     */
    public function getIsRemove()
    {
        return $this->isRemove;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CasesSkinsPickUpUserPUBG
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
     * @return CasesSkinsPickUpUserPUBG
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
     * @return CasesSkinsPickUpUserPUBG
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
     * @return CasesSkinsPickUpUserPUBG
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
