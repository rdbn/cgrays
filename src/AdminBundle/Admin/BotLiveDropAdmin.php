<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BotLiveDropAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_bot-live-drop';
    protected $baseRoutePattern = 'bot-live-drop';

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
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = "{$i}:00";
        }

        $form
            ->add('casesDomain')
            ->add('user', ModelAutocompleteType::class, [
                'property' => 'username'
            ])
            ->add('hourFrom', ChoiceType::class, [
                'choices' => array_flip($hours),
            ])
            ->add('hourTo', ChoiceType::class, [
                'choices' => array_flip($hours),
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('user');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('user')
            ->add('hourFrom')
            ->add('hourTo')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);;
    }
}