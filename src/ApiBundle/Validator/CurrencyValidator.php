<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:35
 */

namespace ApiBundle\Validator;

use AppBundle\Entity\Currency;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrencyValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * DomainIdValidator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $currency = $this->em->getRepository(Currency::class)
            ->findOneBy(['code' => $value]);

        if (!$currency) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }

}