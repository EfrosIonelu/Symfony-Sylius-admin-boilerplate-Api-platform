<?php

namespace App\Twig;

use App\Entity\CustomForm\CustomForm;
use App\Repository\CustomForm\CustomFormRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CustomFormExtension extends AbstractExtension
{
    public function __construct(
        private readonly CustomFormRepository $customFormRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_custom_form_by_code', [$this, 'getCustomFormByCode']),
        ];
    }

    public function getCustomFormByCode(string $code): ?CustomForm
    {
        return $this->customFormRepository->findOneBy(['code' => $code]);
    }
}
