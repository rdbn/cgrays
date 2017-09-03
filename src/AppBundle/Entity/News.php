<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.04.17
 * Time: 10:51
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NewsRepository")
 * @ORM\Table(name="news")
 */
class News
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
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\NewsComment", mappedBy="news")
     */
    protected $newsComment;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->newsComment = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return News
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
     * @return News
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
     * Add newsComment
     *
     * @param \AppBundle\Entity\NewsComment $newsComment
     *
     * @return News
     */
    public function addNewsComment(\AppBundle\Entity\NewsComment $newsComment)
    {
        $this->newsComment[] = $newsComment;

        return $this;
    }

    /**
     * Remove newsComment
     *
     * @param \AppBundle\Entity\NewsComment $newsComment
     */
    public function removeNewsComment(\AppBundle\Entity\NewsComment $newsComment)
    {
        $this->newsComment->removeElement($newsComment);
    }

    /**
     * Get newsComment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNewsComment()
    {
        return $this->newsComment;
    }
}
