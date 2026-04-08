<?php

namespace App\Form;

use App\Entity\StockTransaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RestockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class, [
                'label' => 'Quantity to Add',
                'constraints' => [
                    new NotBlank(message: 'Please enter a quantity'),
                ],
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'e.g. 50',
                    'class' => 'form-control',
                ],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Restock Note (Optional)',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'e.g. Received from Batch #102...',
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockTransaction::class,
        ]);
    }
}
