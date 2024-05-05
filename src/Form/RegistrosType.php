<?php


namespace App\Form;

use App\Entity\Usuarios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nick', TextType::class, [
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?=.*[A-Za-z])[A-Za-z\d]+$/',
                    'message' => 'El nick no puede contener solo números.',
                ]),
            ],
        ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
                'invalid_message' => 'Las contraseñas deben coincidir.',
            'constraints' => [
                new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                    if (strlen($value) < 5) {
                        $context->buildViolation('La contraseña debe tener al menos 5 caracteres.')->addViolation();
                    }

                    if (!preg_match('/[A-Za-z]/', $value)) {
                        $context->buildViolation('La contraseña debe contener al menos una letra.')->addViolation();
                    }
                }),
                new Assert\Regex([
                    'pattern' => '/.*[0-9].*/',
                    'message' => 'La contraseña debe contener al menos un número.',
                ]),
            ],
        ])
         
            ->add('perfil', PerfilesType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuarios::class,
        ]);
    }
}
