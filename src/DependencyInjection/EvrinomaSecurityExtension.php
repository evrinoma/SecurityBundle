<?php


namespace Evrinoma\SecurityBundle\DependencyInjection;

use Evrinoma\SecurityBundle\EvrinomaSecurityBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;


class EvrinomaSecurityExtension extends Extension
{
//region SECTION: Fields
    private ContainerBuilder $container;
//endregion Fields

//region SECTION: Public
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration   = $this->getConfiguration($configs, $container);
        $config          = $this->processConfiguration($configuration, $configs);
        $this->container = $container;

        $this->addDefinition(
            'Evrinoma\SecurityBundle\Configuration\Configuration',
            'evrinoma.security.configuration',
            [
                $config['event']['on_authentication_success'],
                $config['event']['on_authentication_failure'],
                $config['redirect_by_server'],
                $config['firewall_session_key'],
                $config['route']['login'],
                $config['route']['check'],
                $config['route']['redirect'],
                $config['form']['username'],
                $config['form']['password'],
                $config['form']['csrf_token'],

            ],
            true
        );
        if (array_key_exists('ldap_servers', $config) && $ldapServers = $config['ldap_servers']) {
            $definition = $this->container->getDefinition('evrinoma.security.provider.ldap');
            $definition->addMethodCall('setServers', [$ldapServers]);
        }

        if (array_key_exists('LexikJWTAuthenticationBundle', $container->getParameterBag()->get('kernel.bundles'))) {
            $this->addDefinition(
                'Evrinoma\SecurityBundle\Guard\JWT\AuthenticatorGuard',
                'evrinoma.security.guard.jwt',
                [new Reference('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface')],
                true
            );
        }
    }
//endregion Public

//region SECTION: Private
    /**
     * @param string $className
     * @param string $aliasName
     * @param        $arguments
     * @param false  $public
     *
     * @return Definition
     */
    private function addDefinition(string $className, string $aliasName, $arguments, $public = false): Definition
    {
        $definition = new Definition($className);
        $alias      = new Alias($aliasName);

        if ($public) {
            $definition->setPublic(true);
            $alias->setPublic(true);
        }
        $this->container->addDefinitions([$aliasName => $definition]);
        $this->container->addAliases([$className => $alias]);

        foreach ($arguments as $key => $argument) {
            $definition->setArgument($key, $argument);
        }

        return $definition;
    }
//endregion Private

//region SECTION: Getters/Setters
    public function getAlias()
    {
        return EvrinomaSecurityBundle::SECURITY_BUNDLE;
    }
//endregion Getters/Setters
}