<?php

namespace App\Factory;

use App\Entity\Cms\Languages;
use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;
    private OptionsResolver $optionsResolver;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): Languages
    {
        $options = $this->optionsResolver->resolve($options);

        $language = new Languages();
        $language->setName($options['name']);
        $language->setLocale($options['locale']);
        $language->setEnabled($options['enabled']);

        return $language;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', fn () => $this->faker->country)
            ->setDefault('locale', fn () => $this->faker->locale)
            ->setDefault('enabled', true)
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('locale', 'string')
            ->setAllowedTypes('enabled', 'bool');
    }
}
