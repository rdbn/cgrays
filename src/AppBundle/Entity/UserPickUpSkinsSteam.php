<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 05.03.2018
 * Time: 11:40
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserPickUpSkinsSteamRepository")
 * @ORM\Table(name="user_pick_up_skins_steam")
 */
class UserPickUpSkinsSteam
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain", inversedBy="userPickUpSkinsUser")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="userPickUpSkinsUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skins", inversedBy="userPickUpSkinsUser")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    /**
     * UserPickUpSkinsSteam constructor.
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
     * @return UserPickUpSkinsSteam
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
     * Set casesDomain
     *
     * @param \AppBundle\Entity\CasesDomain $casesDomain
     *
     * @return UserPickUpSkinsSteam
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
     * @return UserPickUpSkinsSteam
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

    /**
     * Set skins
     *
     * @param \AppBundle\Entity\Skins $skins
     *
     * @return UserPickUpSkinsSteam
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
}
