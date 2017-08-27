<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 19.07.17
 * Time: 23:00
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="payment")
 * @ORM\Entity()
 */
class Payment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="payment")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PaymentSystem", inversedBy="payment")
     * @ORM\JoinColumn(name="payment_system_id", referencedColumnName="id")
     */
    protected $paymentSystem;

    /**
     * @ORM\Column(name="payment_information", type="string")
     */
    protected $paymentInformation;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * Payment constructor.
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
     * Set paymentInformation
     *
     * @param string $paymentInformation
     *
     * @return Payment
     */
    public function setPaymentInformation($paymentInformation)
    {
        $this->paymentInformation = $paymentInformation;

        return $this;
    }

    /**
     * Get paymentInformation
     *
     * @return string
     */
    public function getPaymentInformation()
    {
        return $this->paymentInformation;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Payment
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
     * Set paymentSystem
     *
     * @param \AppBundle\Entity\PaymentSystem $paymentSystem
     *
     * @return Payment
     */
    public function setPaymentSystem(\AppBundle\Entity\PaymentSystem $paymentSystem = null)
    {
        $this->paymentSystem = $paymentSystem;

        return $this;
    }

    /**
     * Get paymentSystem
     *
     * @return \AppBundle\Entity\PaymentSystem
     */
    public function getPaymentSystem()
    {
        return $this->paymentSystem;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Payment
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
