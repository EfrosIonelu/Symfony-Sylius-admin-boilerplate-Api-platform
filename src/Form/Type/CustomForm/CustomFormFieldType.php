<?php

declare(strict_types=1);

namespace App\Form\Type\CustomForm;

use App\Doctrine\ORM\Type\FieldType;
use App\Form\Type\DataTransformer\JsonToArrayTransformer;
use App\Repository\Cms\LanguagesRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomFormFieldType extends AbstractResourceType
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

        $fieldTypeChoices = [];
        foreach (FieldType::getValues() as $value) {
            $fieldTypeChoices['field.type.'.$value] = $value;
        }

        $builder
            ->add('fieldType', ChoiceType::class, [
                'label' => 'app.ui.field_type',
                'choices' => $fieldTypeChoices,
                'required' => true,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'app.ui.required',
                'required' => false,
            ])
            ->add('order', IntegerType::class, [
                'label' => 'app.ui.order',
                'required' => false,
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('allowedValues', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'app.ui.allowed_values',
                'required' => false,
                'prototype' => true,
                'prototype_name' => '__allowed_value_name__',
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'app.ui.option_value',
                    ],
                ],
            ])
            ->add('attributes', TextareaType::class, [
                'label' => 'app.ui.attributes',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'app.ui.attributes_placeholder_json',
                    'help' => 'app.ui.attributes_help_json',
                ],
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entries' => $entries,
                'entry_type' => CustomFormFieldTranslationType::class,
                'label' => false,
                'by_reference' => false,
            ])
        ;

        $builder->get('attributes')->addModelTransformer(new JsonToArrayTransformer());
    }

    public function getBlockPrefix(): string
    {
        return 'app_custom_form_field';
    }
}
