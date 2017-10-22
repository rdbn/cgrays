<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:34
 */

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SkinsPickUoUserConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Не верный ids скинов.';

    public function validatedBy()
    {
        return CurrencyValidator::class;
    }

}