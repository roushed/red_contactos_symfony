<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\Categorias;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoria', EntityType::class, [
                'class' => Categorias::class,
                'choice_label' => function (Categorias $categoria) {
                    return $categoria->getNombre();
                },
                'label' => 'Categoria',
                'required' => true,
                'placeholder' => 'Seleccione una categoria',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.id NOT IN (:ids)')
                        ->setParameter('ids', $options['excluded_categories']);
                }
            ])
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
            'excluded_categories' => [], 
        ]);
    }
}
