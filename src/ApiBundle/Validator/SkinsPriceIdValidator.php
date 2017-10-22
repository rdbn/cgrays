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

class SkinsPriceIdValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        preg_match('/[^0-9]+/gi', $value, $matches);

        if (!count($matches)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }

}