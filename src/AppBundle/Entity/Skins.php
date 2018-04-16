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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SkinsRepository")
 * @ORM\Table(name="skins")
 */
class Skins
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeSkins", inversedBy="skins")
     * @ORM\JoinColumn(name="type_skins_id", referencedColumnName="id")
     */
    protected $typeSkins;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Decor", inversedBy="skins")
     * @ORM\JoinColumn(name="decor_id", referencedColumnName="id")
     */
    protected $decor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ItemSet", inversedBy="skins")
     * @ORM\JoinColumn(name="item_set_id", referencedColumnName="id")
     */
    protected $itemSet;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Weapon", inversedBy="skins")
     * @ORM\JoinColumn(name="weapon_id", referencedColumnName="id")
     */
    protected $weapon;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rarity", inversedBy="skins")
     * @ORM\JoinColumn(name="rarity_id", referencedColumnName="id")
     */
    protected $rarity;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Quality", inversedBy="skins")
     * @ORM\JoinColumn(name="quality_id", referencedColumnName="id")
     */
    protected $quality;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="icon_url", type="text")
     */
    protected $iconUrl;

    /**
     * @ORM\Column(name="icon_url_large", type="text")
     */
    protected $iconUrlLarge;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(name="steam_price", type="decimal", precision=10, scale=5, options={"default": 0})
     */
    protected $steamPrice;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SkinsPrice", mappedBy="skins")
     */
    protected $skinsPrice;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkins", mappedBy="skins")
     */
    protected $casesSkins;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsDropUser", mappedBy="skins")
     */
    protected $casesSkinsDropUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasesSkinsDropUser", mappedBy="skins")
     */
    protected $userPickUpSkinsUser;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    public function __construct()
    {
        $this->steamPrice = 0;
        $this->skinsPrice = new ArrayCollection();
        $this->casesSkins = new ArrayCollection();
        $this->casesSkinsDropUser = new ArrayCollection();
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

        if ($this->getIconUrlLarge()) {
            $filename = __DIR__ . "/../../../web{$this->getIconUrlLarge()}";
            if (file_exists($filename)) {
                unlink($filename);
            }
        }

        $uploadDir = UploadImageService::UPLOAD_IMAGE_PATH;
        $nameImage = $uploadDir.md5(uniqid(time(), true)).'.png';
        $this->getFile()->move($uploadDir, $nameImage);

        $this->setIconUrl($nameImage);
        $this->setIconUrlLarge($nameImage);
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
     * @return Skins
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
     * Set iconUrl
     *
     * @param string $iconUrl
     *
     * @return Skins
     */
    public function setIconUrl($iconUrl)
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    /**
     * Get iconUrl
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * Set iconUrlLarge
     *
     * @param string $iconUrlLarge
     *
     * @return Skins
     */
    public function setIconUrlLarge($iconUrlLarge)
    {
        $this->iconUrlLarge = $iconUrlLarge;

        return $this;
    }

    /**
     * Get iconUrlLarge
     *
     * @return string
     */
    public function getIconUrlLarge()
    {
        return $this->iconUrlLarge;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Skins
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set steamPrice
     *
     * @param string $steamPrice
     *
     * @return Skins
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
     * Set typeSkins
     *
     * @param \AppBundle\Entity\TypeSkins $typeSkins
     *
     * @return Skins
     */
    public function setTypeSkins(\AppBundle\Entity\TypeSkins $typeSkins = null)
    {
        $this->typeSkins = $typeSkins;

        return $this;
    }

    /**
     * Get typeSkins
     *
     * @return \AppBundle\Entity\TypeSkins
     */
    public function getTypeSkins()
    {
        return $this->typeSkins;
    }

    /**
     * Set decor
     *
     * @param \AppBundle\Entity\Decor $decor
     *
     * @return Skins
     */
    public function setDecor(\AppBundle\Entity\Decor $decor = null)
    {
        $this->decor = $decor;

        return $this;
    }

    /**
     * Get decor
     *
     * @return \AppBundle\Entity\Decor
     */
    public function getDecor()
    {
        return $this->decor;
    }

    /**
     * Set itemSet
     *
     * @param \AppBundle\Entity\ItemSet $itemSet
     *
     * @return Skins
     */
    public function setItemSet(\AppBundle\Entity\ItemSet $itemSet = null)
    {
        $this->itemSet = $itemSet;

        return $this;
    }

    /**
     * Get itemSet
     *
     * @return \AppBundle\Entity\ItemSet
     */
    public function getItemSet()
    {
        return $this->itemSet;
    }

    /**
     * Set weapon
     *
     * @param \AppBundle\Entity\Weapon $weapon
     *
     * @return Skins
     */
    public function setWeapon(\AppBundle\Entity\Weapon $weapon = null)
    {
        $this->weapon = $weapon;

        return $this;
    }

    /**
     * Get weapon
     *
     * @return \AppBundle\Entity\Weapon
     */
    public function getWeapon()
    {
        return $this->weapon;
    }

    /**
     * Set rarity
     *
     * @param \AppBundle\Entity\Rarity $rarity
     *
     * @return Skins
     */
    public function setRarity(\AppBundle\Entity\Rarity $rarity = null)
    {
        $this->rarity = $rarity;

        return $this;
    }

    /**
     * Get rarity
     *
     * @return \AppBundle\Entity\Rarity
     */
    public function getRarity()
    {
        return $this->rarity;
    }

    /**
     * Set quality
     *
     * @param \AppBundle\Entity\Quality $quality
     *
     * @return Skins
     */
    public function setQuality(\AppBundle\Entity\Quality $quality = null)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Get quality
     *
     * @return \AppBundle\Entity\Quality
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Add skinsPrice
     *
     * @param \AppBundle\Entity\SkinsPrice $skinsPrice
     *
     * @return Skins
     */
    public function addSkinsPrice(\AppBundle\Entity\SkinsPrice $skinsPrice)
    {
        $this->skinsPrice[] = $skinsPrice;

        return $this;
    }

    /**
     * Remove skinsPrice
     *
     * @param \AppBundle\Entity\SkinsPrice $skinsPrice
     */
    public function removeSkinsPrice(\AppBundle\Entity\SkinsPrice $skinsPrice)
    {
        $this->skinsPrice->removeElement($skinsPrice);
    }

    /**
     * Get skinsPrice
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkinsPrice()
    {
        return $this->skinsPrice;
    }

    /**
     * Add casesSkin
     *
     * @param \AppBundle\Entity\CasesSkins $casesSkin
     *
     * @return Skins
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

    /**
     * Add casesSkinsDropUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $casesSkinsDropUser
     *
     * @return Skins
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
     * @param \AppBundle\Entity\CasesSkinsDropUser $userPickUpSkinsUser
     *
     * @return Skins
     */
    public function addUserPickUpSkinsUser(\AppBundle\Entity\CasesSkinsDropUser $userPickUpSkinsUser)
    {
        $this->userPickUpSkinsUser[] = $userPickUpSkinsUser;

        return $this;
    }

    /**
     * Remove userPickUpSkinsUser
     *
     * @param \AppBundle\Entity\CasesSkinsDropUser $userPickUpSkinsUser
     */
    public function removeUserPickUpSkinsUser(\AppBundle\Entity\CasesSkinsDropUser $userPickUpSkinsUser)
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
