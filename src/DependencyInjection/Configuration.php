<?php

namespace Werkspot\FacebookAdsBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('werkspot_facebook_ads');

        $rootNode
            ->children()
                ->scalarNode('app_id')->end()
                ->scalarNode('app_secret')->end()
                ->scalarNode('system_user_token')->end()
            ->end();

        return $treeBuilder;
    }
}
