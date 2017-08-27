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

class IdItemValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!isset($value['id'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        $ids = explode(':', $value['id']);
        $classId = (int) $ids[0];
        $instanceId = (int) $ids[1];
        $assetId = (int) $ids[2];

        if ($classId == 0 && $instanceId == 0 && $assetId == 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

    }
}