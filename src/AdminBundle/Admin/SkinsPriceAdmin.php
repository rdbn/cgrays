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

class SkinsPriceAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_skins-price';
    protected $baseRoutePattern = 'skins-price';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('users', 'sonata_type_model_autocomplete', [
                'property' => 'username'
            ])
            ->add('skins', 'sonata_type_model_autocomplete', [
                'property' => 'name'
            ])
            ->add('classId')
            ->add('assetId')
            ->add('instanceId')
            ->add('price')
            ->add('isSell')
            ->add('isRemove');
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('skins.name')
            ->add('skins.quality')
            ->add('skins.weapon')
            ->add('skins.typeSkins')
            ->add('skins.itemSet')
            ->add('skins.decor')
        ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('skins.name')
            ->add('skins.rarity')
            ->add('skins.icon_url', 'string', [
                'template' => 'CRUD/list__icon_url.html.twig'
            ])
            ->add('isSell')
            ->add('isRemove')
            ->add('createdAt', 'date', [
                'format' => 'Y-m-d H:i:s',
            ]);
    }

    /**
     * @param string $context
     * @return ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $repository = $this->getConfigurationPool()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(SkinsPrice::class);

        $query = new ProxyQuery($repository->querySonata());
        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        return $query;
    }
}