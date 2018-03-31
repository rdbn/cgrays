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
     * Unmapped property to handle file uploads
     */
    private $file;

    public function __construct()
    {
        $this->steamPrice = 0;
    }

    public function __toString()
    {
        return $this->name;
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
     * Set description
     *
     * @param string $description
     *
     * @return SkinsPUBG
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
}
