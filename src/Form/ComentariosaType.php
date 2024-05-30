<?php

namespace App\Form;
use App\Entity\Comentariosa;
use App\Entity\Actividades;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ComentariosaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('texto', TextareaType::class, [
            'label' => false,
            'attr' => [
            'rows' => 6,
            'cols' => 85, 
    ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comentariosa::class,
        ]);
    }
}
