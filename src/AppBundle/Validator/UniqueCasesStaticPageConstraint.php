<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:34
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCasesStaticPageConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Такая страница для данного домена уже есть.';

    public function validatedBy()
    {
        return UniqueCasesStaticPageValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}