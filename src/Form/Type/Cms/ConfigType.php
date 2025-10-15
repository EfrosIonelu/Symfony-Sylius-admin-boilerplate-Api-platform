<?php

namespace App\Form\Type\Cms;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => 'Keyword',
                'required' => true,
            ])
            ->add('value', TextType::class, [
                'label' => 'Value',
                'required' => true,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_config';
    }
}
