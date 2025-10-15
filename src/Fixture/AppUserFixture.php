<?php

declare(strict_types=1);

namespace App\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class AppUserFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'user_user';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $nodeBuilder = $resourceNode->children();
        $nodeBuilder->scalarNode('email')->cannotBeEmpty();
        $nodeBuilder->booleanNode('enabled');
        $nodeBuilder->scalarNode('password')->cannotBeEmpty();
        $nodeBuilder->scalarNode('first_name')->cannotBeEmpty();
        $nodeBuilder->scalarNode('last_name')->cannotBeEmpty();
        $nodeBuilder->scalarNode('customer')->cannotBeEmpty();
        $nodeBuilder->scalarNode('role')->cannotBeEmpty();
    }
}
