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
     * @ORM\Column(name="steam_api_key", type="string", length=255)
     */
    protected $steamApiKey;

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

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsPickUpUser", mappedBy="casesDomain")
     */
    protected $casesSkinsPickUpUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsDropUser", mappedBy="casesDomain")
     */
    protected $casesSkinsDropUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserPickUpSkinsSteam", mappedBy="casesDomain")
     */
    protected $userPickUpSkinsUser;

    /**
     * CasesDomain constructor.
     */
    public function __construct()
    {
        $this->uuid = GenerateUUID::getUUID();
        $this->createdAt = new \DateTime();
        $this->cases = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->domain;
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
     * Set steamApiKey
     *
     * @param string $steamApiKey
     *
     * @return CasesDomain
     */
    public function setSteamApiKey($steamApiKey)
    {
        $this->steamApiKey = $steamApiKey;

        return $this;
    }

    /**
     * Get steamApiKey
     *
     * @return string
     */
    public function getSteamApiKey()
    {
        return $this->steamApiKey;
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

    /**
     * Add casesStaticPage
     *
     * @param \AppBundle\Entity\CasesStaticPage $casesStaticPage
     *
     * @return CasesDomain
     */
    public function addCasesStaticPage(\AppBundle\Entity\CasesStaticPage $casesStaticPage)
    {
        $this->casesStaticPage[] = $casesStaticPage;

        return $this;
    }

    /**
     * Remove casesStaticPage
     *
     * @param \AppBundle\Entity\CasesStaticPage $casesStaticPage
     */
    public function removeCasesStaticPage(\AppBundle\Entity\CasesStaticPage $casesStaticPage)
    {
        $this->casesStaticPage->removeElement($casesStaticPage);
    }

    /**
     * Get casesStaticPage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesStaticPage()
    {
        return $this->casesStaticPage;
    }

    /**
     * Add casesSkinsPickUpUser
     *
     * @param \AppBundle\Entity\CasesSkinsPickUpUser $casesSkinsPickUpUser
     *
     * @return CasesDomain
     */
    public function addCasesSkinsPickUpUser(\AppBundle\Entity\CasesSkinsPickUpUser $casesSkinsPickUpUser)
    {
        $this->casesSkinsPickUpUser[] = $casesSkinsPickUpUser;

        return $this;
    }

    /**
     * Remove casesSkinsPickUpUser
     *
     * @param \AppBundle\Entity\CasesSkinsPickUpUser $casesSkinsPickUpUser
     */
    public function removeCasesSkinsPickUpUser(\AppBundle\Entity\CasesSkinsPickUpUser $casesSkinsPickUpUser)
    {
        $this->casesSkinsPickUpUser->removeElement($casesSkinsPickUpUser);
    }

    /**
     * Get casesSkinsPickUpUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesSkinsPickUpUser()
    {
        return $this->casesSkinsPickUpUser;
    }

    /**
     * Add casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser
     *
     * @return CasesDomain
     */
    public function addCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser)
    {
        $this->casesSkinsDropUser[] = $casesSkinsDropUser;

        return $this;
    }

    /**
     * Remove casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser
     */
    public function removeCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser)
    {
        $this->casesSkinsDropUser->removeElement($casesSkinsDropUser);
    }

    /**
     * Get casesSkinsDropUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesSkinsDropUser()
    {
        return $this->casesSkinsDropUser;
    }

    /**
     * Add userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser
     *
     * @return CasesDomain
     */
    public function addUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser[] = $userPickUpSkinsUser;

        return $this;
    }

    /**
     * Remove userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser
     */
    public function removeUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteam $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser->removeElement($userPickUpSkinsUser);
    }

    /**
     * Get userPickUpSkinsUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserPickUpSkinsUser()
    {
        return $this->userPickUpSkinsUser;
    }
}
