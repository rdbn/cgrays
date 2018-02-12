<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Skins;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatisticCasesFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $date = new \DateTime();

        $builder->setMethod('GET');
        $builder
            ->add('date_from', TextType::class, [
                'label' => false,
                'data' => isset($options['data']) ? $options['data']['date_from'] : $date->format('Y-m-d 00:00:00'),
            ])
            ->add('date_to', TextType::class, [
                'label' => false,
                'data' => isset($options['data']) ? $options['data']['date_from'] : $date->format('Y-m-d 23:59:59'),
            ])
            ->add('group', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Дате' => 'date',
                    'пользователям' => 'user',
                    'кейсам' => 'cases',
                    'скинам' => 'skins',
                    'Категориям кейсов' => 'cases_category',
                    'Доменам кейсов' => 'cases_domain',
                ],
                'choices_as_values' => true,
                'data' => 'date'
            ])
            ->add('user', EntityType::class, [
                'label' => false,
                'class' => User::class,
                'choice_label' => 'username'
            ])
            ->add('cases', EntityType::class, [
                'label' => false,
                'class' => Cases::class,
                'choice_label' => 'name'
            ])
            ->add('skins', EntityType::class, [
                'label' => false,
                'class' => Skins::class,
                'choice_label' => 'name'
            ])
            ->add('cases_category', EntityType::class, [
                'label' => false,
                'class' => CasesCategory::class,
                'choice_label' => 'name'
            ])
            ->add('cases_domain', EntityType::class, [
                'label' => false,
                'class' => CasesDomain::class,
                'choice_label' => 'domain'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
