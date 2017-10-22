<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:34
 */

namespace ApiBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SkinsPriceIdConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Не верный skins_price_id.';

    public function validatedBy()
    {
        return CurrencyValidator::class;
    }

}