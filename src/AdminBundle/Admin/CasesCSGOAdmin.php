<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Cases;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class CasesCSGOAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_cases';
    protected $baseRoutePattern = 'cases';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
            ->add('casesCategory')
            ->add('casesDomain')
            ->add('createdAt');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('price')
            ->add('promotionPrice')
            ->add('coefficient')
            ->add('image', 'string', [
                'template' => 'CRUD/list__image.html.twig'
            ])
            ->add('createdAt', 'date', [
                'format' => 'Y-m-d H:i:s',
            ])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }
}