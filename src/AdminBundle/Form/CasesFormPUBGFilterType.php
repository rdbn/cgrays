<?php

namespace AdminBundle\Form;

use AppBundle\Entity\RarityPUBG;
use AppBundle\Entity\SkinsPUBG;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CasesFormPUBGFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название скина',
                'required' => false,
            ])
            ->add('rarity', EntityType::class, [
                'class' => RarityPUBG::class,
                'choice_label' => 'localizedTagName',
                'label' => 'Раритетность',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SkinsPUBG::class,
            'csrf_protection' => false,
        ]);
    }
}
