<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 25.02.2018
 * Time: 14:16
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BotLiveDropRepository")
 * @ORM\Table(name="bot_live_drop")
 */
class BotLiveDrop
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="hour_from", type="smallint")
     */
    protected $hourFrom;

    /**
     * @ORM\Column(name="hour_to", type="smallint")
     */
    protected $hourTo;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * BotLiveDrop constructor.
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
     * Set hourFrom
     *
     * @param integer $hourFrom
     *
     * @return BotLiveDrop
     */
    public function setHourFrom($hourFrom)
    {
        $this->hourFrom = $hourFrom;

        return $this;
    }

    /**
     * Get hourFrom
     *
     * @return integer
     */
    public function getHourFrom()
    {
        return $this->hourFrom;
    }

    /**
     * Set hourTo
     *
     * @param integer $hourTo
     *
     * @return BotLiveDrop
     */
    public function setHourTo($hourTo)
    {
        $this->hourTo = $hourTo;

        return $this;
    }

    /**
     * Get hourTo
     *
     * @return integer
     */
    public function getHourTo()
    {
        return $this->hourTo;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BotLiveDrop
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
     * @return BotLiveDrop
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
     * @return BotLiveDrop
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
