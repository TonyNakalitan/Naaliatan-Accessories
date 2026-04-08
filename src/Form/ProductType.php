<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'constraints' => [
                    new NotBlank(message: 'Please enter a product name'),
                ],
            ])
            ->add('productCode', TextType::class, [
                'label' => 'Product Code',
                'constraints' => [
                    new NotBlank(message: 'Please enter a product code'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(message: 'Please enter a product description'),
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price (₱)',
                'currency' => 'PHP',
                'constraints' => [
                    new NotBlank(message: 'Please enter a price'),
                    new Positive(message: 'Price must be a positive number'),
                ],
            ])
            ->add('character', EntityType::class, [
                'label' => 'Character',
                'class' => Character::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a character',
                'constraints' => [
                    new NotBlank(message: 'Please select a character'),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image(
                        maxSize: '10M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        mimeTypesMessage: 'Please upload a valid image file (JPEG, PNG, GIF)'
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
