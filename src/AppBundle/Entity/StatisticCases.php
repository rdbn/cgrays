<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.02.2018
 * Time: 19:14
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatisticCasesRepository")
 * @ORM\Table(name="statistic_cases")
 */
class StatisticCases
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date_at", type="datetime")
     */
    protected $dateAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cases")
     * @ORM\JoinColumn(name="cases_id", referencedColumnName="id")
     */
    protected $cases;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesSkins")
     * @ORM\JoinColumn(name="cases_skins_id", referencedColumnName="id")
     */
    protected $casesSkins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesCategory")
     * @ORM\JoinColumn(name="cases_category_id", referencedColumnName="id")
     */
    protected $casesCategory;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Skins")
     * @ORM\JoinColumn(name="skins_id", referencedColumnName="id")
     */
    protected $skins;

    /**
     * @ORM\Column(name="hit_cases", type="integer")
     */
    protected $hitCases;

    /**
     * @ORM\Column(name="open_cases", type="integer")
     */
    protected $openCases;

    /**
     * @ORM\Column(name="pick_up_skins", type="integer")
     */
    protected $pickUpSkins;

    /**
     * @ORM\Column(name="sell_skins", type="decimal", precision=20, scale=5)
     */
    protected $sellSkins;

    /**
     * StatisticCases constructor.
     */
    public function __construct()
    {
        $this->dateAt = new \DateTime();
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
     * Set dateAt
     *
     * @param \DateTime $dateAt
     *
     * @return StatisticCases
     */
    public function setDateAt($dateAt)
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    /**
     * Get dateAt
     *
     * @return \DateTime
     */
    public function getDateAt()
    {
        return $this->dateAt;
    }

    /**
     * Set hitCases
     *
     * @param integer $hitCases
     *
     * @return StatisticCases
     */
    public function setHitCases($hitCases)
    {
        $this->hitCases = $hitCases;

        return $this;
    }

    /**
     * Get hitCases
     *
     * @return integer
     */
    public function getHitCases()
    {
        return $this->hitCases;
    }

    /**
     * Set openCases
     *
     * @param integer $openCases
     *
     * @return StatisticCases
     */
    public function setOpenCases($openCases)
    {
        $this->openCases = $openCases;

        return $this;
    }

    /**
     * Get openCases
     *
     * @return integer
     */
    public function getOpenCases()
    {
        return $this->openCases;
    }

    /**
     * Set pickUpSkins
     *
     * @param integer $pickUpSkins
     *
     * @return StatisticCases
     */
    public function setPickUpSkins($pickUpSkins)
    {
        $this->pickUpSkins = $pickUpSkins;

        return $this;
    }

    /**
     * Get pickUpSkins
     *
     * @return integer
     */
    public function getPickUpSkins()
    {
        return $this->pickUpSkins;
    }

    /**
     * Set sellSkins
     *
     * @param string $sellSkins
     *
     * @return StatisticCases
     */
    public function setSellSkins($sellSkins)
    {
        $this->sellSkins = $sellSkins;

        return $this;
    }

    /**
     * Get sellSkins
     *
     * @return string
     */
    public function getSellSkins()
    {
        return $this->sellSkins;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return StatisticCases
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
     * Set cases
     *
     * @param \AppBundle\Entity\Cases $cases
     *
     * @return StatisticCases
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

    /**
     * Set casesSkins
     *
     * @param \AppBundle\Entity\CasesSkins $casesSkins
     *
     * @return StatisticCases
     */
    public function setCasesSkins(\AppBundle\Entity\CasesSkins $casesSkins = null)
    {
        $this->casesSkins = $casesSkins;

        return $this;
    }

    /**
     * Get casesSkins
     *
     * @return \AppBundle\Entity\CasesSkins
     */
    public function getCasesSkins()
    {
        return $this->casesSkins;
    }

    /**
     * Set casesDomain
     *
     * @param \AppBundle\Entity\CasesDomain $casesDomain
     *
     * @return StatisticCases
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
     * Set casesCategory
     *
     * @param \AppBundle\Entity\CasesCategory $casesCategory
     *
     * @return StatisticCases
     */
    public function setCasesCategory(\AppBundle\Entity\CasesCategory $casesCategory = null)
    {
        $this->casesCategory = $casesCategory;

        return $this;
    }

    /**
     * Get casesCategory
     *
     * @return \AppBundle\Entity\CasesCategory
     */
    public function getCasesCategory()
    {
        return $this->casesCategory;
    }

    /**
     * Set skins
     *
     * @param \AppBundle\Entity\Skins $skins
     *
     * @return StatisticCases
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
