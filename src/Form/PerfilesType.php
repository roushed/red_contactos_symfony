<?php
    // PerfilesType.php
    namespace App\Form;

    use App\Entity\Perfiles;
    use App\Entity\Aficiones;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use App\Entity\Municipios;
    use Symfony\Component\Validator\Constraints as Assert;
    

    class PerfilesType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('nombre', TextType::class, [
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^\D+$/',
                            'message' => 'El nombre no puede contener números o símbolos.',
                        ]),
                    ],
                ])
                ->add('apellidos', TextType::class, [
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^\D+$/',
                            'message' => 'Los apellidos no pueden contener números o símbolos.',
                        ]),
                    ],
                ])

                ->add('email', TextType::class, [
                    'label' => 'Email',
                    'required' => true, 
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                            'message' => 'El email "{{ value }}" no es válido.',
                        ]),
                    ],
                ])
                
                ->add('edad', DateType::class, [
                    'label' => 'Fecha de Nacimiento',
                    'widget' => 'single_text', 
                    'html5' => true, 
                    'attr' => [
                        'class' => 'datepicker', 
                        'placeholder' => 'Seleccione fecha de nacimiento', 
                    ],
                    'constraints' => [
                        new Assert\LessThan([
                            'value' => 'now -12 years', 
                            'message' => 'Debes tener de tener una edad mínima para registrarte.',
                        ]),
                    ],
                ])
               
                ->add('genero', ChoiceType::class, [
                    'choices' => [
                        'Hombre' => 'Hombre',
                        'Mujer' => 'Mujer',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Género',
                    'label_attr' => [
                        'class' => 'd-flex flex-row align-items-center', 
                    ],
                ])
           
                
                ->add('ciudad', EntityType::class, [
                    'class' => Municipios::class,
                    'choice_label' => 'ciudad',
                    'label' => 'Ciudad',
                    'required' => true,
                    'placeholder' => 'Seleccione una ciudad',
                    'mapped' => false, 
                ])
                ->add('descripcion')
                ->add('foto', FileType::class, [
                    'label' => 'Adjuntar Imagen',
                    'mapped' => false, 
                    'required' => false, 
                    'attr' => [
                        'accept' => 'image/*', 
                    ],
                ])
                ->add('aficiones', EntityType::class, [
                    'class' => Aficiones::class,
                    'choice_label' => 'nombre',
                    'label' => 'Aficiones',
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false, 
                   
                ]);

        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Perfiles::class,
            ]);
        }
    }