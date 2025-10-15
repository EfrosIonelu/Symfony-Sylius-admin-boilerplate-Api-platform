<?php

declare(strict_types=1);

namespace App\Form\Type\CustomForm;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomFormTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app.ui.name',
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_custom_form_translation';
    }
}
