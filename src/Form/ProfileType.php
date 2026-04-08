<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('displayName', TextType::class, [
                'label' => 'Display Name',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('zodiacSign', ChoiceType::class, [
                'label' => 'Zodiac Sign',
                'choices' => [
                    'Aries' => 'Aries',
                    'Taurus' => 'Taurus',
                    'Gemini' => 'Gemini',
                    'Cancer' => 'Cancer',
                    'Leo' => 'Leo',
                    'Virgo' => 'Virgo',
                    'Libra' => 'Libra',
                    'Scorpio' => 'Scorpio',
                    'Sagittarius' => 'Sagittarius',
                    'Capricorn' => 'Capricorn',
                    'Aquarius' => 'Aquarius',
                    'Pisces' => 'Pisces',
                ],
                'placeholder' => 'Select your zodiac sign',
                'required' => false,
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF)',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'New Password (leave empty to keep current)',
                'attr' => ['placeholder' => 'Enter new password to change'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
