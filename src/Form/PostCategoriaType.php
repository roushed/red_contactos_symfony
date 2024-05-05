<?php

namespace App\Form;

use App\Entity\Categorias;
use App\Entity\Posts;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PostCategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('subject', null, [
            'required' => true,
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^(?![0-9]+$)(?![\W]+$)[a-zA-Z0-9\s\W]+$/',
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
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
