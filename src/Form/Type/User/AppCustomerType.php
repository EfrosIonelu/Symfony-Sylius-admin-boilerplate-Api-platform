<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Entity\User\AppCustomer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $builder->getData();
        if ($data?->getId()) {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'app.ui.email',
                    'required' => true,
                    'disabled' => true,
                ]);
        } else {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'app.ui.email',
                    'required' => true,
                ]);
        }

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'app.ui.first_name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'app.ui.last_name',
                'required' => false,
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'app.ui.birthday',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'app.ui.gender',
                'required' => false,
                'choices' => [
                    'app.ui.gender.male' => 'm',
                    'app.ui.gender.female' => 'f',
                    'app.ui.gender.other' => 'o',
                ],
                'placeholder' => 'app.ui.select_option',
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'app.ui.phone_number',
                'required' => false,
            ])
            ->add('user', AppUserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AppCustomer::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_customer';
    }
}
