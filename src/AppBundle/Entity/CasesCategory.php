<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use AppBundle\Modal\DictionaryInterface;
use AppBundle\Services\UploadImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesCategoryRepository")
 * @ORM\Table(name="cases_category")
 */
class CasesCategory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(name="sort_number", type="smallint")
     */
    protected $sortNumber;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Cases", mappedBy="casesCategory")
     */
    protected $cases;

    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->name;
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
     * Set name
     *
     * @param string $name
     *
     * @return CasesCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sortNumber
     *
     * @param string $sortNumber
     *
     * @return CasesCategory
     */
    public function setSortNumber($sortNumber)
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    /**
     * Get sortNumber
     *
     * @return string
     */
    public function getSortNumber()
    {
        return $this->sortNumber;
    }

    /**
     * Add case
     *
     * @param \AppBundle\Entity\Cases $case
     *
     * @return CasesCategory
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
