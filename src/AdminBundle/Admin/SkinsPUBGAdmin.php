<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\SkinsPUBG;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SkinsPUBGAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_skins-pubg';
    protected $baseRoutePattern = 'skins-pubg';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureFormFields(FormMapper $form)
    {
        /* @var SkinsPUBG $skins */
        $skins = $this->getSubject();

        $form
            ->add('name')
            ->add('steamPrice')
            ->add('rarity')
            ->add('file', FileType::class, [
                'help' => "<img src='/{$skins->getImage()}' alt='{$skins->getName()}' />"
            ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
            ->add('rarity');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('rarity')
            ->add('image', 'string', [
                'template' => 'CRUD/list__skins_image.html.twig'
            ]);
    }

    /**
     * @param SkinsPUBG $object
     */
    public function prePersist($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param SkinsPUBG $object
     */
    public function preUpdate($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param SkinsPUBG $object
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
            ->getRepository(SkinsPUBG::class);

        $query = new ProxyQuery($repository->querySonata());
        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        return $query;
    }
}