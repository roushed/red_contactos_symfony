<?php

namespace App\Form;

use App\Entity\Usuarios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nick', null, [
            'attr' => [
                'class' => 'form-field',
                'placeholder' => 'Introduce tu Nick',
            ],
            'label' => false,
        ])
        ->add('password', PasswordType::class, [ 
            'attr' => [
                'class' => 'form-field',
                'placeholder' => 'Introduce tu Password',
            ],
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuarios::class,
        ]);
    }
}
