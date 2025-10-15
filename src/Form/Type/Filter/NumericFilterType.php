<?php

namespace App\Form\Type\Filter;

use Sylius\Component\Grid\Filter\NumericRangeFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumericFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('greaterThan', NumberType::class, [
                'label' => 'app.ui.greater_than',
                'required' => false,
                'scale' => $options['scale'],
                'rounding_mode' => $options['rounding_mode'],
                'attr' => $options['greater_than_attr'],
                'property_path' => '[greaterThan]',
            ])
            ->add('lessThan', NumberType::class, [
                'label' => 'app.ui.less_than',
                'required' => false,
                'scale' => $options['scale'],
                'rounding_mode' => $options['rounding_mode'],
                'attr' => $options['less_than_attr'],
                'property_path' => '[lessThan]',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'scale' => NumericRangeFilter::DEFAULT_SCALE,
                'rounding_mode' => NumericRangeFilter::DEFAULT_ROUNDING_MODE,
                'greater_than_attr' => [],
                'less_than_attr' => [],
            ])
            ->setAllowedTypes('scale', ['string', 'int'])
            ->setAllowedTypes('rounding_mode', ['string', 'int'])
            ->setAllowedTypes('greater_than_attr', ['array'])
            ->setAllowedTypes('less_than_attr', ['array'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_grid_filter_numeric';
    }
}
