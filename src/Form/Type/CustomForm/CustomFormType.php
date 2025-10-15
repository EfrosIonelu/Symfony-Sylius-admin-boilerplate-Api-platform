<?php

declare(strict_types=1);

namespace App\Form\Type\CustomForm;

use App\Repository\Cms\LanguagesRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomFormType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups = [],
        private readonly ?LanguagesRepository $languagesRepository = null,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entries = [];
        $languages = $this->languagesRepository->findAll();
        foreach ($languages as $language) {
            $entries[] = $language->getLocale();
        }

        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'app.ui.enabled',
                'required' => false,
            ])
            ->add('code', TextType::class, [
                'label' => 'app.ui.code',
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entries' => $entries,
                'entry_type' => CustomFormTranslationType::class,
                'label' => 'app.ui.translations',
                'by_reference' => false,
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => CustomFormFieldType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__field_item_name__',
                'attr' => [
                    'class' => 'collection-fields',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_custom_form';
    }
}
