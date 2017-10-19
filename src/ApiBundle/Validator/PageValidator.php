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

class PageValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!isset($value['page'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $page = (int) $value['page'];
        if ($page == 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }
}