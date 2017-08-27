<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MessageTypeForm extends AbstractType
{
    /**
     * @var User
     */
    private $user;

    /**
     * MessageTypeForm constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', HiddenType::class, [
                'label' => false,
                'data' => $this->user->getUsername(),
            ])
            ->add('text_message', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Введите сообщенаие'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить',
                'attr' => ['class' => 'btn btn-action', 'placeholder' => 'Пополнить'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {}
}
