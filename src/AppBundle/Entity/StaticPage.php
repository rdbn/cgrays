<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use AppBundle\Modal\DictionaryInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StaticPageRepository")
 * @ORM\Table(name="static_page")
 */
class StaticPage
{
    const TYPE_PAGE = [
        'vacancies' => 'Вакансии',
        'information' => 'Информация',
        'termsOfUse' => 'Пользовательское соглашение',
    ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type_page", type="string", length=45, unique=true)
     */
    protected $typePage;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

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
     * Set typePage
     *
     * @param string $typePage
     *
     * @return StaticPage
     */
    public function setTypePage($typePage)
    {
        $this->typePage = $typePage;

        return $this;
    }

    /**
     * Get typePage
     *
     * @return string
     */
    public function getTypePage()
    {
        return $this->typePage;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return StaticPage
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StaticPage
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
}
