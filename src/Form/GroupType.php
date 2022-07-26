<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Technique;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->required = $options['required'];

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
            ->add('adress', null, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('zip', null, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
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
            ->add('link', null, [
                'label' => 'Site internet',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('techniques', EntityType::class, [
                'label' => 'Techniques',
                'class' => Technique::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('images', FileType::class, [
                'label' => 'Images',
                'required' => $this->required,
                'multiple' => true,
                'mapped' => false,
                'constraints' =>
                new All([
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez importer des images au format jpg/jpeg ou png',
                    ])
                ])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'group_item',
            'required' => null,
        ]);
    }
}
