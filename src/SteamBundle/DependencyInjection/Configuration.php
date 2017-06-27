<?php

namespace SteamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('steam')
            ->children()
                ->scalarNode('steam_api_url')->end()
                ->scalarNode('steam_community_url')->end()
                ->scalarNode('steam_inventory_count')->end()
                ->scalarNode('steam_key')->end()
                ->scalarNode('user_class')->end()
            ->end();

        return $treeBuilder;
    }
}
