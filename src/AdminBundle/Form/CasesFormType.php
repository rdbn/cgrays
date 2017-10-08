<?php

namespace AdminBundle\Form;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesDomain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasesFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название',
            ])
            ->add('casesCategory', EntityType::class, [
                'label' => 'Категории',
                'class' => CasesCategory::class,
                'choice_label' => 'name',
            ])
            ->add('casesDomain', EntityType::class, [
                'label' => 'Список сайтов',
                'class' => CasesDomain::class,
                'choice_label' => 'domain',
            ])
            ->add('price', TextType::class, [
                'label' => 'Цена',
            ])
            ->add('file', FileType::class, [
                'label' => 'Картинка для кейса',
                'required' => false,
            ])
            ->add('sort', HiddenType::class)
            ->add("submit", SubmitType::class, [
                'label' => 'Сохранить',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cases::class
        ]);
    }
}
