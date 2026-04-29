<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Character Name',
                'constraints' => [
                    new NotBlank(message: 'Please enter a character name'),
                ],
            ])
            ->add('creator', TextType::class, [
                'label' => 'Creator',
                'constraints' => [
                    new NotBlank(message: 'Please enter creator name'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(message: 'Please enter a character description'),
                ],
            ])
            ->add('alignment', ChoiceType::class, [
                'label' => 'Alignment',
                'choices' => Character::getAlignmentChoices(),
                'placeholder' => 'Select alignment',
                'constraints' => [
                    new NotBlank(message: 'Please select an alignment'),
                ],
            ])
            ->add('colorCode', TextType::class, [
                'label' => 'Color Code (Hex)',
                'attr' => [
                    'placeholder' => 'FF5733',
                    'pattern' => '^#?[0-9A-Fa-f]{6}$',
                    'maxlength' => '7',
                ],
                'constraints' => [
                    new NotBlank(message: 'Please enter a color code'),
                    new Regex(
                        pattern: '/^#?[0-9A-Fa-f]{6}$/',
                        message: 'Please enter a valid hex color code (e.g., FF5733)'
                    ),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Character Image',
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
            'data_class' => Character::class,
        ]);
    }
}
