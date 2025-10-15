<?php

namespace App\Component\Locale;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;

class LocaleProvider implements LocaleProviderInterface
{
    public function getAvailableLocalesCodes(): array
    {
        return ['en', 'fr'];
    }

    public function getDefaultLocaleCode(): string
    {
        return 'en';
    }
}
