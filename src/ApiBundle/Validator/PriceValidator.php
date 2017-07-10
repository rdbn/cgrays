<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 06.07.17
 * Time: 10:26
 */

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PriceValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!isset($value['price'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $priceLength = strlen((int) $value['price']);
        if ($priceLength < 1 && $priceLength > 9) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

    }
}