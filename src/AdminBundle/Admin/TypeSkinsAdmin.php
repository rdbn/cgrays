<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Skins;
use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TypeSkinsAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_type-skins';
    protected $baseRoutePattern = 'type-skins';

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
            ->add('localizedTagName');
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
            ->add('localizedTagName');
    }
}