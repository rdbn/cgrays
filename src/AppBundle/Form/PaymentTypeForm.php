<?php

namespace AppBundle\Form;

use AppBundle\Entity\Payment;
use AppBundle\Entity\PaymentSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentTypeForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentSystem', EntityType::class, [
                'label' => false,
                'class' => PaymentSystem::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Выберите способ оплаты.',
            ])
            ->add('sum_payment', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Сумма для пополнения'],
                'mapped' => false,
                'data' => 1000,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Пополнить',
                'attr' => ['class' => 'btn btn-action', 'placeholder' => 'Пополнить'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Payment::class
        ]);
    }
}
