<?php

namespace Mach\Bundle\NwlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mach_nwl');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
                ->children()
                    ->scalarNode('user')->isRequired()->end()
                    ->scalarNode('password')->isRequired()->end()
                    ->scalarNode('url')->isRequired()->end()
                    ->scalarNode('doctrine_connection')
                        ->defaultValue('doctrine.orm.default_entity_manager')
                    ->end()
                ->end();


        return $treeBuilder;
    }
}
