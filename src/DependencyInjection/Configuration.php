<?php

namespace Evrinoma\SecurityBundle\DependencyInjection;

use Evrinoma\SecurityBundle\EvrinomaSecurityBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
//region SECTION: Getters/Setters
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(EvrinomaSecurityBundle::SECURITY_BUNDLE);
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('firewall_session_key')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('route')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('login')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('check')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('redirect')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('username')->isRequired()->cannotBeEmpty()->defaultValue('_username')->end()
                        ->scalarNode('password')->isRequired()->cannotBeEmpty()->defaultValue('_password')->end()
                        ->scalarNode('csrf_token')->isRequired()->cannotBeEmpty()->defaultValue('_csrf_token')->end()
                    ->end()
                ->end()
                ->booleanNode('redirect_by_server')->defaultTrue()->end()
                ->arrayNode('event')->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('on_authentication_failure')->isRequired()->defaultFalse()->end()
                        ->booleanNode('on_authentication_success')->isRequired()->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('ldap_servers')
                    ->useAttributeAsKey('name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->prototype('array')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
//endregion Getters/Setters
}
