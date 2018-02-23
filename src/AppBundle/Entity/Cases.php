<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use AppBundle\Services\UploadImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasesRepository")
 * @ORM\Table(name="cases")
 */
class Cases
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesCategory", inversedBy="cases")
     * @ORM\JoinColumn(name="cases_category_id", referencedColumnName="id")
     */
    protected $casesCategory;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CasesDomain", inversedBy="cases")
     * @ORM\JoinColumn(name="cases_domain_id", referencedColumnName="id")
     */
    protected $casesDomain;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(name="promotion_price", type="decimal", precision=7, scale=2)
     */
    protected $promotionPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $image;

    /**
     * @ORM\Column(type="text")
     */
    protected $sort;

    /**
     * @ORM\Column(name="created_at", type="datetime", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkins", mappedBy="cases")
     */
    protected $casesSkins;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->casesSkins = new ArrayCollection();
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if ($this->getImage()) {
            $filename = __DIR__ . "/../../../web{$this->getImage()}";
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        $uploadDir = UploadImageService::UPLOAD_IMAGE_PATH;
        $nameImage = $uploadDir.md5(uniqid(time(), true)).'.png';
        $this->getFile()->move($uploadDir, $nameImage);

        $this->image = $nameImage;
        $this->setFile(null);
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
     * @return Cases
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
     * Set price
     *
     * @param string $price
     *
     * @return Cases
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set promotionPrice
     *
     * @param string $promotionPrice
     *
     * @return Cases
     */
    public function setPromotionPrice($promotionPrice)
    {
        $this->promotionPrice = $promotionPrice;

        return $this;
    }

    /**
     * Get promotionPrice
     *
     * @return string
     */
    public function getPromotionPrice()
    {
        return $this->promotionPrice;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Cases
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set sort
     *
     * @param string $sort
     *
     * @return Cases
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Cases
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
     * Set casesCategory
     *
     * @param \AppBundle\Entity\CasesCategory $casesCategory
     *
     * @return Cases
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
     * Set casesDomain
     *
     * @param \AppBundle\Entity\CasesDomain $casesDomain
     *
     * @return Cases
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
     * Add casesSkin
     *
     * @param \AppBundle\Entity\CasesSkins $casesSkin
     *
     * @return Cases
     */
    public function addCasesSkin(\AppBundle\Entity\CasesSkins $casesSkin)
    {
        $this->casesSkins[] = $casesSkin;

        return $this;
    }

    /**
     * Remove casesSkin
     *
     * @param \AppBundle\Entity\CasesSkins $casesSkin
     */
    public function removeCasesSkin(\AppBundle\Entity\CasesSkins $casesSkin)
    {
        $this->casesSkins->removeElement($casesSkin);
    }

    /**
     * Get casesSkins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCasesSkins()
    {
        return $this->casesSkins;
    }
}
