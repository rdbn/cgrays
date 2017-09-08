<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 04.09.17
 * Time: 23:22
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_users';
    protected $baseRoutePattern = 'users';

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
            ->add('username')
            ->add('steamId')
            ->add('isOnline')
            ->add('isSell')
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'role',
                'multiple' => true
            ])
            ->add('file', 'file');
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('username')
            ->add('steamId')
            ->add('isOnline')
            ->add('isSell');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('username', 'string', [
                'label' => 'Username пользоваетля',
            ])
            ->add('steamId', 'integer', [
                'label' => 'Steam ID пользователся',
            ])
            ->add('isOnline', 'boolean', [
                'label' => 'Онлайн или нет'
            ])
            ->add('isSell', 'boolean', [
                'label' => 'Включен трейд или нет'
            ])
            ->add('roles', 'collection', [
                'label' => 'Права пользователя',
            ])
            ->add('lastOnline', 'date', [
                'label' => 'Время последнего захода',
                'format' => 'Y-m-d H:i:s',
            ])
            ->add('createdAt', 'date', [
                'label' => 'Дата регистрации',
                'format' => 'Y-m-d H:i:s',
            ])
            ->add('_action', null, [
                'actions' => [
                    'switch_user' => [
                        'template' => 'CRUD/list__action_switch_user.html.twig'
                    ],
                ],
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
            ->getRepository(User::class);

        $query = new ProxyQuery($repository->querySonata());
        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        return $query;
    }

    /**
     * @param User $object
     */
    public function prePersist($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param User $object
     */
    public function preUpdate($object)
    {
        $this->manageFileUpload($object);
    }

    /**
     * @param User $object
     */
    private function manageFileUpload($object)
    {
        if ($object->getFile()) {
            $object->upload();
        }
    }
}