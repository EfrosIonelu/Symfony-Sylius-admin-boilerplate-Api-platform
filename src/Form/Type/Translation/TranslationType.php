<?php

declare(strict_types=1);

namespace App\Form\Type\Translation;

use App\Repository\Cms\LanguagesRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class TranslationType extends AbstractResourceType
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
            ->add('key', TextType::class, [
                'label' => 'app.ui.translation_key',
                'required' => true,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entries' => $entries,
                'entry_type' => TranslationTranslationType::class,
                'label' => 'app.ui.translations',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_translation';
    }
}
