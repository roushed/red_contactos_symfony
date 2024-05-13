<?php

namespace App\Form;

use App\Entity\Categorias;
use App\Entity\Posts;
use App\Entity\Municipios;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert; 


class PostCategoriaArticulosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('subject', null, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?![0-9]+$)[a-zA-Z0-9\s\W]+$/',
                    'message' => 'El texto no puede contener solo simbolos o números.',
                ]),
            ],
        ])
        ->add('texto', null, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?![0-9]+$)(?![\W]+$)[a-zA-Z0-9\s\W]+$/',
                    'message' => 'El texto no puede contener solo simbolos o números.',
                ]),
            ],
        ])
        
        ->add('precio', TextType::class, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^\d+(\.\d+)?$/',
                    'message' => 'El precio debe ser un número válido.',
                ]),
            ],
        ])

        ->add('telefono', TextType::class, [
            'required' => false,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'El teléfono debe contener solo números.',
                ]),
                new Assert\Length([
                    'max' => 9,
                    'maxMessage' => 'El teléfono no puede tener más de {{ limit }} números.',
                ]),
            ],
        ])

        ->add('ciudad', EntityType::class, [
            'class' => Municipios::class,
            'choice_label' => 'ciudad',
            'label' => 'Ciudad',
            'required' => true,
            'placeholder' => 'Seleccione un Municipio',
            'mapped' => false, 
        ])
            
        ->add('imagenes', FileType::class, [ // Cambia MultipleFileType por FileType
            'label' => 'Arrastra y suelta o haz click para seleccionar imágenes.',
            'required' => false, 
            'mapped' => false, 
            'multiple' => true, 
            'attr' => [
                'id' => 'imagenes',
                'accept' => 'image/*',
                'style' => 'display: none;',
                
            ],
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
