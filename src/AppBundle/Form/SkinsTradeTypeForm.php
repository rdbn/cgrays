<?php

namespace AppBundle\Form;

use AppBundle\Entity\SkinsTrade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkinsTradeTypeForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_rule', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить',
                'attr' => ['class' => 'btn btn-action', 'placeholder' => 'Пополнить'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SkinsTrade::class
        ]);
    }
}
