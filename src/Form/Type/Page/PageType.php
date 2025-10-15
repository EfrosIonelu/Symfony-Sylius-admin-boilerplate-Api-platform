<?php

declare(strict_types=1);

namespace App\Form\Type\Page;

use App\Entity\EntityMedia\PageMedia;
use App\Form\Type\MultiZoneEntityMediaType;
use App\Repository\Cms\LanguagesRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PageType extends AbstractResourceType
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
            ->add('translations', ResourceTranslationsType::class, [
                'entries' => $entries,
                'entry_type' => PageTranslationType::class,
                'label' => 'app.ui.translations',
            ])
            ->add('files', MultiZoneEntityMediaType::class, [
                'zones' => ['main', 'slider'],
                'entity_class' => PageMedia::class,
            ])
        ;

        //        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
        //            $data = $event->getData();
        //            dd($data);
        //        });
    }

    public function getBlockPrefix(): string
    {
        return 'app_page';
    }
}
