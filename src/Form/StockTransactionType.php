<?php

namespace App\Form;

use App\Entity\StockTransaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adjustmentType', ChoiceType::class, [
                'label' => 'Adjustment Type',
                'choices' => [
                    'Add to Stock (+)' => 'positive',
                    'Remove from Stock (-)' => 'negative',
                ],
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantity',
                'constraints' => [
                    new NotBlank(message: 'Please enter a quantity'),
                ],
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'e.g. 5',
                    'class' => 'form-control',
                ],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockTransaction::class,
        ]);
    }
}
