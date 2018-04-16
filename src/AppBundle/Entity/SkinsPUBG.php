<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.04.17
 * Time: 10:28
 */

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use AppBundle\Services\UploadImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkinsPUBGRepository")
 * @ORM\Table(name="skins_pubg")
 */
class SkinsPUBG
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RarityPUBG", inversedBy="skins")
     * @ORM\JoinColumn(name="rarity_id", referencedColumnName="id")
     */
    protected $rarity;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $image;

    /**
     * @ORM\Column(name="steam_price", type="decimal", precision=21, scale=2, options={"default": 0})
     */
    protected $steamPrice;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsPUBG", mappedBy="skins")
     */
    protected $casesSkins;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsDropUserPUBG", mappedBy="skins")
     */
    protected $casesSkinsDropUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsPickUpUserPUBG", mappedBy="skins")
     */
    protected $casesSkinsPickUpUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserPickUpSkinsSteamPUBG", mappedBy="skins")
     */
    protected $userPickUpSkinsUser;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    public function __construct()
    {
        $this->steamPrice = 0;
    }

    public function __toString()
    {
        return (string) $this->name;
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

        $this->setImage($nameImage);
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
     * @return SkinsPUBG
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
     * Set image
     *
     * @param string $image
     *
     * @return SkinsPUBG
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
     * Set steamPrice
     *
     * @param string $steamPrice
     *
     * @return SkinsPUBG
     */
    public function setSteamPrice($steamPrice)
    {
        $this->steamPrice = $steamPrice;

        return $this;
    }

    /**
     * Get steamPrice
     *
     * @return string
     */
    public function getSteamPrice()
    {
        return $this->steamPrice;
    }

    /**
     * Set rarity
     *
     * @param \AppBundle\Entity\RarityPUBG $rarity
     *
     * @return SkinsPUBG
     */
    public function setRarity(\AppBundle\Entity\RarityPUBG $rarity = null)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return \AppBundle\Entity\RarityPUBG
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * Add casesSkin
     *
     * @param \AppBundle\Entity\CasesSkinsPUBG $casesSkin
     *
     * @return SkinsPUBG
     */
    public function addCasesSkin(\AppBundle\Entity\CasesSkinsPUBG $casesSkin)
    {
        $this->casesSkins[] = $casesSkin;

        return $this;
    }

    /**
     * Remove casesSkin
     *
     * @param \AppBundle\Entity\CasesSkinsPUBG $casesSkin
     */
    public function removeCasesSkin(\AppBundle\Entity\CasesSkinsPUBG $casesSkin)
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

    /**
     * Add casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUserPUBG $casesSkinsDropUser
     *
     * @return SkinsPUBG
     */
    public function addCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUserPUBG $casesSkinsDropUser)
    {
        $this->casesSkinsDropUser[] = $casesSkinsDropUser;

        return $this;
    }

    /**
     * Remove casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUserPUBG $casesSkinsDropUser
     */
    public function removeCasesSkinsDropUser(\AppBundle\Entity\CasesSkinsDropUserPUBG $casesSkinsDropUser)
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
     * Add casesSkinsPickUpUser
     *
     * @param \AppBundle\Entity\CasesSkinsPickUpUserPUBG $casesSkinsPickUpUser
     *
     * @return SkinsPUBG
     */
    public function addCasesSkinsPickUpUser(\AppBundle\Entity\CasesSkinsPickUpUserPUBG $casesSkinsPickUpUser)
    {
        $this->casesSkinsPickUpUser[] = $casesSkinsPickUpUser;

        return $this;
    }

    /**
     * Remove casesSkinsPickUpUser
     *
     * @param \AppBundle\Entity\CasesSkinsPickUpUserPUBG $casesSkinsPickUpUser
     */
    public function removeCasesSkinsPickUpUser(\AppBundle\Entity\CasesSkinsPickUpUserPUBG $casesSkinsPickUpUser)
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
     * Add userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteamPUBG $userPickUpSkinsUser
     *
     * @return SkinsPUBG
     */
    public function addUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteamPUBG $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser[] = $userPickUpSkinsUser;

        return $this;
    }

    /**
     * Remove userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\UserPickUpSkinsSteamPUBG $userPickUpSkinsUser
     */
    public function removeUserPickUpSkinsUser(\AppBundle\Entity\UserPickUpSkinsSteamPUBG $userPickUpSkinsUser)
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
