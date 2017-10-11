<?php

namespace AppBundle\Form;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\Skins;
use AppBundle\Entity\Quality;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkinsFilterTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod("GET");

        $builder
            ->add("name", TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => ["class" => "form-control", "placeholder" => "Поиск по названию"]
            ])
            ->add("typeSkins", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => TypeSkins::class,
                "choice_label" => "localizedTagName",
                "placeholder" => "Тип",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("decor", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Decor::class,
                "choice_label" => "localizedTagName",
                "placeholder" => "Оформление",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("rarity", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Rarity::class,
                "choice_label" => "localizedTagName",
                "placeholder" => "Редкость",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("itemSet", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => ItemSet::class,
                "choice_label" => "localizedTagName",
                "placeholder" => "Набор",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("quality", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Quality::class,
                "choice_label" => "localizedTagName",
                "placeholder" => "Категория",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skins::class
        ]);
    }
}
