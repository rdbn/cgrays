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
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SkinsAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_skins';
    protected $baseRoutePattern = 'skins';

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
        /* @var Skins $skins */
        $skins = $this->getSubject();

        $form
            ->add('name')
            ->add('description', CKEditorType::class)
            ->add('steamPrice')
            ->add('typeSkins')
            ->add('decor')
            ->add('itemSet')
            ->add('weapon')
            ->add('rarity')
            ->add('quality')
            ->add('file', FileType::class, [
                'help' => "<img src='/{$skins->getIconUrlLarge()}' alt='{$skins->getName()}' />"
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
            ->add('rarity')
            ->add('quality')
            ->add('weapon')
            ->add('typeSkins')
            ->add('itemSet')
            ->add('decor');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('rarity')
            ->add('weapon')
            ->add('typeSkins')
            ->add('itemSet')
            ->add('quality')
            ->add('decor')
            ->add('icon_url', 'string', [
                'template' => 'CRUD/list__icon_url.html.twig'
            ]);
    }

    /**
     * @param Skins $object
     */
    public function prePersist($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param Skins $object
     */
    public function preUpdate($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param Skins $object
     */
    private function manageFileUpload($object)
    {
        if ($object->getFile()) {
            $object->upload();
        }
    }

    /**
     * @param string $context
     * @return ProxyQuery
     */
    public function createQuery($context = 'list')
    {
        $repository = $this->getConfigurationPool()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(Skins::class);

        $query = new ProxyQuery($repository->querySonata());
        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        return $query;
    }
}