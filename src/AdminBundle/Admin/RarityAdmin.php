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

class RarityAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_rarity';
    protected $baseRoutePattern = 'rarity';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('internalName')
            ->add('localizedTagName')
            ->add('color');
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('localizedTagName');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('internalName')
            ->add('localizedTagName')
            ->add('color');
    }
}