<?php

namespace App\Form;

use App\Entity\Reporting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->function = $options['function'];

        $builder
            ->add('reporter', null, [
                'label' => 'Auteur du signalement (facultatif)',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'required' => false
            ])
            ->add('reporterContact', null, [
                'label' => 'Contact de l\'auteur du signalement (facultatif)',
                'required' => false
            ]);
        if ($this->function === "update") {
            $builder->add('groupe');
            $builder->add('praticien');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reporting::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'report_item',
            'function' => null,
        ]);
    }
}
