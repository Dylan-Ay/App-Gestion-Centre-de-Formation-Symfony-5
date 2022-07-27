<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Program;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberDays', IntegerType:: class)
            ->add('session', EntityType::class,[
                'class' => Session::class
                ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'placeholder' => 'Selectionnez le module'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}