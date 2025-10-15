<?php

declare(strict_types=1);

namespace App\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class TranslationFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'app_translation';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $nodeBuilder = $resourceNode->children();
        $nodeBuilder->scalarNode('key')->cannotBeEmpty();
        $nodeBuilder->arrayNode('translations')
            ->useAttributeAsKey('locale')
            ->scalarPrototype()->end();
    }
}
