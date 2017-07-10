<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 06.07.17
 * Time: 10:25
 */

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraint;

class IdItemConstraint extends Constraint
{
    public $message = 'Неверное заначение id.';

    public function validatedBy()
    {
        return IdItemValidator::class;
    }
}