<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\User\AppAdministrationRole;
use App\Entity\User\AppUser;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusRbacPlugin\Access\Model\OperationType;
use Odiseo\SyliusRbacPlugin\Model\Permission;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class AdminRoleFixture extends AbstractFixture
{
    public function __construct(
        private readonly ObjectManager $objectManager,
    ) {
    }

    public function load(array $options): void
    {
        $adminRole = new AppAdministrationRole();
        $adminRole->setName($options['name']);

        $this->addPermission($adminRole, 'app_rbac');
        $this->addPermission($adminRole, 'app_config');

        $this->objectManager->persist($adminRole);
        $this->objectManager->flush();

        $defaultPermission = $this->objectManager->getRepository(AppAdministrationRole::class)->findOneBy(['name' => 'No sections access']);
        $users = $this->objectManager->getRepository(AppUser::class)->findAll();

        foreach ($users as $user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setAdministrationRole($adminRole);
            } else {
                $user->setAdministrationRole($defaultPermission);
            }
        }
        $this->objectManager->flush();
    }

    public function getName(): string
    {
        return 'fix_roles';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $nodeBuilder = $optionsNode->children();
        $nodeBuilder->scalarNode('name')->defaultValue('Admin Full Access');
    }

    private function addPermission(AppAdministrationRole $adminRole, string $type): void
    {
        $appRbacPermission = Permission::ofType($type, [
            OperationType::read(),
            OperationType::write(),
        ]);

        $adminRole->addPermission($appRbacPermission);
    }
}
