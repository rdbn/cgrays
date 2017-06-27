<?php

namespace AppBundle\Form;

use AppBundle\Entity\Heroes;
use AppBundle\Entity\Product;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterTypeForm extends AbstractType
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
                "class" => Heroes::class,
                "choice_label" => "title",
                "placeholder" => "Выберите героя",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("typeProduct", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => TypeProduct::class,
                "choice_label" => "name",
                "placeholder" => "Тип",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("quality", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Quality::class,
                "choice_label" => "title",
                "placeholder" => "Качество",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ])
            ->add("rarity", EntityType::class, [
                "label" => false,
                "required" => false,
                "class" => Rarity::class,
                "choice_label" => "title",
                "placeholder" => "Раритетность",
                "attr" => ["class" => "form-control selectpicker", "data-live-search" => "true"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}
