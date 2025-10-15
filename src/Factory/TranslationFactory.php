<?php

namespace App\Factory;

use App\Entity\Cms\Translation;
use App\Entity\Cms\TranslationTranslation;
use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;
    private OptionsResolver $optionsResolver;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): Translation
    {
        $options = $this->optionsResolver->resolve($options);

        $translation = new Translation();
        $translation->setKey($options['key']);

        // Create translation translations for each locale if provided
        if (isset($options['translations']) && is_array($options['translations'])) {
            foreach ($options['translations'] as $locale => $value) {
                $translationTranslation = new TranslationTranslation();
                $translationTranslation->setLocale($locale);
                $translationTranslation->setValue($value);
                $translation->addTranslation($translationTranslation);
            }
        }

        return $translation;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('key', fn () => 'app.'.$this->faker->word.'.message')
            ->setDefault('translations', [])
            ->setAllowedTypes('key', 'string')
            ->setAllowedTypes('translations', 'array');
    }
}
