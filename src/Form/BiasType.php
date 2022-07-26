<?php

namespace App\Form;

use App\Entity\Bias;
use App\Entity\BiasCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BiasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => array('class' => "tiny"), 'required' => false,  'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                    ]),
                ]
            ])
            ->add('biasCategory', EntityType::class, [
                'label' => 'Catégorie de biais',
                'class' => BiasCategory::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bias::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'bias_item',
        ]);
    }
}
