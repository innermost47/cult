<?php

namespace App\Form;

use App\Entity\YoutubeChannel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class YoutubeChannelType extends AbstractType
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
                'label' => 'Lien YouTube',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('facebookPage', null, [
                'label' => 'Page Facebook',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('site', null, [
                'label' => 'Lien Site',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => $this->required,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez importer une image au format jpg/jpeg ou png',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => YoutubeChannel::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'youtube_channel_item',
            'required' => null,
        ]);
    }
}
