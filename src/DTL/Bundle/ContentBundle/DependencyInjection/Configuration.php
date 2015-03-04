<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DTL\Bundle\ContentBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dtl_content');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('compat')
                    ->info('Enable legacy compatibility services')
                    ->canBeEnabled()
                ->end()
                ->arrayNode('phpcr_odm')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('namespaces')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('content')->defaultValue('ncon')->end()
                                ->scalarNode('localized-content')->defaultValue('lcon')->end()
                                ->scalarNode('system')->defaultValue('nsys')->end()
                                ->scalarNode('localized-system')->defaultValue('lsys')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('structure')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('paths')
                            ->isRequired()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('path')
                                        ->example('%kernel.root_dir%/Resources/templates')
                                    ->end()
                                    ->scalarNode('type')
                                        ->defaultValue('page')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}

