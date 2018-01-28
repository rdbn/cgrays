<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasesFormFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название скина',
                'required' => false,
            ])
            ->add('rarity', EntityType::class, [
                'class' => Rarity::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Редкость',
                'required' => false,
            ])
            ->add('itemSet', EntityType::class, [
                'class' => ItemSet::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Набор',
                'required' => false,
            ])
            ->add('quality', EntityType::class, [
                'class' => Quality::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Категория',
                'required' => false,
            ])
            ->add('typeSkins', EntityType::class, [
                'class' => TypeSkins::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Тип',
                'required' => false,
            ])
            ->add('weapon', EntityType::class, [
                'class' => Weapon::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Оружие',
                'required' => false,
            ])
            ->add('decor', EntityType::class, [
                'class' => Decor::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Оформление',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skins::class
        ]);
    }
}
