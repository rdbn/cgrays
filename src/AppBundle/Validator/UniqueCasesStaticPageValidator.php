<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.10.17
 * Time: 21:35
 */

namespace AppBundle\Validator;

use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\CasesStaticPage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCasesStaticPageValidator extends ConstraintValidator
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
     * @param CasesStaticPage $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $typePage = $value->getTypePage();
        $uuid = $value->getCasesDomain()->getUuid();

        $casesDomain = $this->em->getRepository(CasesStaticPage::class)
            ->findStaticPageByDomainIdAndPageName($typePage, $uuid);

        if (!$casesDomain) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }

}