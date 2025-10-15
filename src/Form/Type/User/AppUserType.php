<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Doctrine\ORM\Type\RoleType;
use App\Entity\User\AppAdministrationRole;
use App\Entity\User\AppUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('administrationRole', EntityType::class, [
                'class' => AppAdministrationRole::class,
                'choice_label' => 'name',
                'label' => 'app.ui.administration_role',
                'required' => false,
                'placeholder' => 'app.ui.select_option',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('ar')
                        ->orderBy('ar.name', 'ASC');
                },
            ])
            ->add('username', TextType::class, [
                'label' => 'app.ui.username',
                'required' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'app.ui.enabled',
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'app.ui.roles',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'role.type.ROLE_USER' => RoleType::ROLE_USER,
                    'role.type.ROLE_ADMIN' => RoleType::ROLE_ADMIN,
                    'role.type.ROLE_ORGANIZATION' => RoleType::ROLE_ORGANIZATION,
                    'role.type.ROLE_INTERNAL' => RoleType::ROLE_INTERNAL,
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if (null === $user || null === $user->getId()) {
                $form->add('plainPassword', TextType::class, [
                    'label' => 'app.ui.password',
                    'required' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AppUser::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_user';
    }
}
