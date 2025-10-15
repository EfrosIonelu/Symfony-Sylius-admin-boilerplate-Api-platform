<?php

declare(strict_types=1);

namespace App\Form\Type\Page;

use MonsieurBiz\SyliusRichEditorPlugin\Form\Type\RichEditorType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PageTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app.ui.name',
                'required' => false,
            ])
            ->add('content', RichEditorType::class, [
                'label' => 'app.ui.content',
                'required' => false,
                'attr' => [
                    'placeholder' => 'app.ui.add_page_content',
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_page_translation';
    }
}
