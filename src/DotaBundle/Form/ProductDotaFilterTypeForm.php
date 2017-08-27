<?php

namespace DotaBundle\Form;

use DotaBundle\Entity\HeroesDota;
use DotaBundle\Entity\ProductDota;
use DotaBundle\Entity\QualityDota;
use DotaBundle\Entity\RarityDota;
use DotaBundle\Entity\TypeProductDota;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductDotaFilterTypeForm extends AbstractType
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
            ->add("heroes", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => HeroesDota::class,
                "choice_label" => "title",
                "placeholder" => "Выберите героя",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("typeProduct", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => TypeProductDota::class,
                "choice_label" => "name",
                "placeholder" => "Тип",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("quality", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => QualityDota::class,
                "choice_label" => "title",
                "placeholder" => "Качество",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("rarity", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => RarityDota::class,
                "choice_label" => "title",
                "placeholder" => "Раритетность",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductDota::class
        ]);
    }
}
