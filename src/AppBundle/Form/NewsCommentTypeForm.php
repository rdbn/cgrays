<?php

namespace AppBundle\Form;

use AppBundle\Entity\NewsComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsCommentTypeForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Написать коментарий']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить',
                'attr' => ['class' => 'btn btn-action'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NewsComment::class
        ]);
    }
}
