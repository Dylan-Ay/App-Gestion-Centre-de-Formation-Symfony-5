<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Program;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberDays', IntegerType:: class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Durée (en jour)'
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'label' => 'Intitulé du module',
                'placeholder' => 'Selectionnez le module',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}