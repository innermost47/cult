<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ]
            ])
            ->add('pseudo', null, [
                'label' => 'Pseudo',
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 255,
                    ]),
                ]
            ])
            ->add(
                'role',
                ChoiceType::class,
                [
                    'mapped' => false,
                    'required' => true,
                    'empty_data' => '',
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'choices' => [
                        'Utilisateur' => 'user',
                        'Administrateur' => 'admin',
                    ],
                ]
            )
            ->add("adfi", null, [
                'label' => 'Adfi',
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'user_item',
        ]);
    }
}
