<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\EntityMedia\PageMedia;
use App\Form\DataTransformer\MultiZoneMediaTransformer;
use App\Repository\Cms\MediaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiZoneEntityMediaType extends AbstractType
{
    public function __construct(
        private readonly MediaRepository $mediaRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Add hidden fields for each zone
        foreach ($options['zones'] as $zone) {
            $builder->add($zone, HiddenType::class, [
                'attr' => [
                    'data-zone' => $zone,
                ],
            ]);
        }

        $transformer = new MultiZoneMediaTransformer(
            $this->mediaRepository,
            $options['zones'],
            $options['entity_class']
        );
        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['zones'] = $options['zones'];
        $view->vars['entity_class'] = $options['entity_class'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'zones' => ['main', 'slider'],
            'entity_class' => PageMedia::class,
            'compound' => true,
        ]);

        $resolver->setAllowedTypes('zones', 'array');
        $resolver->setAllowedTypes('entity_class', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'multi_zone_entity_media';
    }
}
