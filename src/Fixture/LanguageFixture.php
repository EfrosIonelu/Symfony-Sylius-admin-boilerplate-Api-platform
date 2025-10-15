<?php

declare(strict_types=1);

namespace App\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class LanguageFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'app_language';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $nodeBuilder = $resourceNode->children();
        $nodeBuilder->scalarNode('name')->cannotBeEmpty();
        $nodeBuilder->scalarNode('locale')->cannotBeEmpty();
        $nodeBuilder->booleanNode('enabled');
    }
}
