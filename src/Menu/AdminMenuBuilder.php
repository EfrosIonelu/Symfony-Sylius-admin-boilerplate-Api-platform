<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'sylius_admin_ui.knp.menu_builder')]
final readonly class AdminMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu
            ->addChild('dashboard', [
                'route' => 'sylius_admin_ui_dashboard',
            ])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'tabler:dashboard')
        ;

        $this->addCmsSubMenu($menu);
        $this->addCustomFormsSubMenu($menu);
        $this->addAdministrationSubMenu($menu);

        return $menu;
    }

    private function addCmsSubMenu(ItemInterface $menu): void
    {
        $cms = $menu
            ->addChild('library')
            ->setLabel('app.ui.cms')
            ->setLabelAttribute('icon', 'tabler:books')
        ;

        $cms->addChild('configs', ['route' => 'app_admin_config_index'])
            ->setLabel('app.ui.configs')
            ->setLabelAttribute('icon', 'book')
            ->setExtra('routes', [
                'app_admin_config_index',
                'app_admin_config_create',
                'app_admin_config_update',
            ])
        ;

        $cms->addChild('languages', ['route' => 'app_admin_languages_index'])
            ->setLabel('app.ui.languages')
            ->setLabelAttribute('icon', 'book')
            ->setExtra('routes', [
                'app_admin_languages_index',
                'app_admin_languages_create',
                'app_admin_languages_update',
            ])
        ;

        $cms->addChild('translations', ['route' => 'app_admin_translation_index'])
            ->setLabel('app.ui.translations')
            ->setLabelAttribute('icon', 'book')
            ->setExtra('routes', [
                'app_admin_translation_index',
                'app_admin_translation_create',
                'app_admin_translation_update',
            ])
        ;

        $cms->addChild('media', ['route' => 'app_admin_media_index'])
            ->setLabel('app.ui.media')
            ->setLabelAttribute('icon', 'book')
            ->setExtra('routes', [
                'app_admin_media_index',
                'app_admin_media_update',
            ])
        ;

        $cms->addChild('page', ['route' => 'app_admin_page_index'])
            ->setLabel('app.ui.page')
            ->setLabelAttribute('icon', 'web')
            ->setExtra('routes', [
                'app_admin_page_index',
                'app_admin_page_update',
            ])
        ;
    }

    private function addAdministrationSubMenu(ItemInterface $menu): void
    {
        $rbac = $menu
            ->addChild('administration')
            ->setLabel('app.ui.administration')
            ->setLabelAttribute('icon', 'tabler:shield-lock')
        ;

        $rbac->addChild('customers', ['route' => 'app_admin_app_customer_index'])
            ->setLabel('app.ui.app_customers')
            ->setLabelAttribute('icon', 'tabler:users')
            ->setExtra('routes', [
                'app_admin_app_customer_index',
                'app_admin_app_customer_create',
                'app_admin_app_customer_update',
            ])
        ;

        $rbac->addChild('administration_roles', ['route' => 'sylius_rbac_admin_administration_role_index'])
            ->setLabel('app.ui.administration_roles')
            ->setLabelAttribute('icon', 'tabler:users-group')
            ->setExtra('routes', [
                'sylius_rbac_admin_administration_role_index',
                'sylius_rbac_admin_administration_role_create_view',
                'sylius_rbac_admin_administration_role_create',
                'sylius_rbac_admin_administration_role_update_view',
                'sylius_rbac_admin_administration_role_update',
            ])
        ;
    }

    private function addCustomFormsSubMenu(ItemInterface $menu): void
    {
        $customForms = $menu
            ->addChild('custom_forms')
            ->setLabel('app.ui.custom_forms')
            ->setLabelAttribute('icon', 'tabler:forms')
        ;

        $customForms->addChild('custom_form', ['route' => 'app_admin_custom_form_index'])
            ->setLabel('app.ui.custom_forms')
            ->setLabelAttribute('icon', 'tabler:forms')
            ->setExtra('routes', [
                'app_admin_custom_form_index',
                'app_admin_custom_form_create',
                'app_admin_custom_form_update',
            ])
        ;

        $customForms->addChild('custom_form_submission', ['route' => 'app_admin_custom_form_submission_index'])
            ->setLabel('app.ui.custom_form_submissions')
            ->setLabelAttribute('icon', 'tabler:file-text')
            ->setExtra('routes', [
                'app_admin_custom_form_submission_index',
                'app_admin_custom_form_submission_show',
            ])
        ;
    }
}
