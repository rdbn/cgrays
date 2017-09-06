<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\StaticPage;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StaticPageAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_static-page';
    protected $baseRoutePattern = 'static-page';

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
            ->add('typePage', ChoiceType::class, [
                'choices' => array_flip(StaticPage::TYPE_PAGE),
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
            ->add('createdAt');
    }
}