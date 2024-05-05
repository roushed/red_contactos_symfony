<?php

namespace App\Form;

use App\Entity\Actividades;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Asegúrate de importar FileType
use App\Entity\Municipios;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActividadesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nombre', null, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?![0-9]+$)(?![\W]+$)[a-zA-Z0-9\s\W]+$/',
                    'message' => 'El texto no puede contener solo simbolos o números.',
                ]),
            ],
        ])
        ->add('descripcion', null, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?![0-9]+$)(?![\W]+$)[a-zA-Z0-9\s\W]+$/',
                    'message' => 'El texto no puede contener solo simbolos o números.',
                ]),
            ],
        ])
            ->add('imagen', FileType::class, [
                'label' => 'Adjuntar Imagen',
                'mapped' => false, 
                'required' => false, 
                'attr' => [
                    'accept' => 'image/*', 
                ],
            ])
            ->add('fecha', DateType::class, [
                'required' => true,
                'constraints' => [
                    
                    new Assert\GreaterThan([
                        'value' => 'yesterday',
                        'message' => 'La fecha debe ser igual o posterior a la fecha actual.',
                    ]),
                ],
            ])
            ->add('hora', TimeType::class, [
                'required' => true,
                
            ])
            ->add('direccion', null, [
                'required' => true,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(?![0-9]+$)(?![\W]+$)[a-zA-Z0-9\s\W]+$/',
                        'message' => 'El texto no puede contener solo simbolos o números.',
                    ]),
                ],
            ])
            ->add('municipio', EntityType::class, [
                'class' => Municipios::class,
                'choice_label' => 'ciudad',
                'label' => 'Ciudad',
                'required' => true,
                'placeholder' => 'Seleccione una ciudad',
                'mapped' => false, 
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Actividades::class,
        ]);
    }
}
