<?php

namespace App\Form\Type;

use App\Entity\Category;
use App\Form\Model\ProductModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('quantity', IntegerType::class)
            ->add('price', IntegerType::class)
            ->add('vat', TextType::class)
            ->add('short_description', TextType::class)
            ->add('description', TextType::class)
            ->add('category', EntityType::class,
                [
                    'class' => Category::class,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductModel::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
