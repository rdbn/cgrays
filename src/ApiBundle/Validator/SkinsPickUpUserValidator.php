<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:35
 */

namespace ApiBundle\Validator;

use AppBundle\Entity\CasesSkinsPickUpUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SkinsPickUpUserValidator extends ConstraintValidator
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
        preg_match("//", $value, $matches);
        if (count($matches) > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $skinsPickUpUser = $this->em->getRepository(CasesSkinsPickUpUser::class)
            ->findSkinsForUpdateByIds($value);

        if (!count($skinsPickUpUser)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }

}