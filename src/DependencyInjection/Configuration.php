<?php

namespace Evrinoma\SecurityBundle\DependencyInjection;

use Evrinoma\SecurityBundle\EvrinomaSecurityBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function PHPUnit\Framework\throwException;

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
                ->append($this->getLexikNode())
            ->end();

        return $treeBuilder;
    }

    private function getLexikNode()
    {
        $treeBuilder = new TreeBuilder('token');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->isRequired()
            ->addDefaultsIfNotSet()
            ->treatFalseLike(['enabled' => false])
            ->treatTrueLike(['enabled' => true])
            ->treatNullLike(['jwt' => ['enabled' => false]])
             ->validate()
            ->ifTrue(function ($v) {
                if (is_array($v) && (count($v) === 1)) {
                    return true;
                }
                return false;
            })
            ->then(function ($v){
                if (array_key_exists('enabled',$v) && $v['enabled']) {
                        throw new \InvalidArgumentException(sprintf('token required options are missing, just only enabled', json_encode($v))) ;
                }
                return $v;
            })
            ->end()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('jwt')
            ->treatNullLike(['enabled' => false])
            ->validate()
            ->ifTrue(function ($v) {
                if (is_array($v) && (count($v) === 1)) {
                    return true;
                }

                return false;
            })
            ->then(function ($v){
                if (array_key_exists('enabled',$v) && !$v['enabled']) {
                    throw new \InvalidArgumentException(sprintf('jwt option is missing or token option require false value', json_encode($v)));
                }

                return $v;
            })
            ->end()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('access_ttl')->end()
                        ->scalarNode('refresh_ttl')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('domain')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
         ;


        return $rootNode;
    }
//endregion Getters/Setters
}
