<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 06.07.17
 * Time: 10:24
 */

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraint;

class PageConstraint extends Constraint
{
    public $message = 'Неверное заначение цены.';

    public function validatedBy()
    {
        return PageValidator::class;
    }
}