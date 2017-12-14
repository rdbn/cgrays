<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\CasesStaticPage;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CasesStaticPageAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_cases-static-page';
    protected $baseRoutePattern = 'cases-static-page';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('casesDomain', EntityType::class, [
                'class' => CasesDomain::class,
                'choice_label' => 'domain'
            ])
            ->add('typePage', ChoiceType::class, [
                'choices' => array_flip(CasesStaticPage::TYPE_PAGE),
                'placeholder' => 'Выберите тип страницы'
            ])
            ->add('text', CKEditorType::class, [
                'config' => array(
                    'uiColor' => '#ffffff',
                ),
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('typePage')
            ->add('casesDomain.domain')
            ->add('createdAt', 'date', [
                'format' => 'Y-m-d H:i:s',
            ]);
    }
}