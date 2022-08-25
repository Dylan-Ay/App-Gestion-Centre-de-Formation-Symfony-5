<?php

namespace App\Form;

use App\Entity\Session;
use App\Form\ProgramType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_start', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('date_end', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('place_number')
            ->add('formation')
            ->add('programs', CollectionType::class, [
                'entry_type' => ProgramType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
