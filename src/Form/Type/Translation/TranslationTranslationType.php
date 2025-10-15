<?php

declare(strict_types=1);

namespace App\Form\Type\Translation;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class TranslationTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', TextareaType::class, [
                'label' => 'app.ui.value',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_translation_translation';
    }
}
