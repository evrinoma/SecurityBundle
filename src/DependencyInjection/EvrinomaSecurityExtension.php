<?php


namespace Evrinoma\SecurityBundle\DependencyInjection;

use Evrinoma\SecurityBundle\EvrinomaSecurityBundle;
use Evrinoma\UtilsBundle\DependencyInjection\HelperTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;


class EvrinomaSecurityExtension extends Extension
{
    use HelperTrait;

//region SECTION: Public
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration   = $this->getConfiguration($configs, $container);
        $config          = $this->processConfiguration($configuration, $configs);

        $this->addDefinition(
            $container,
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
            $definition = $container->getDefinition('evrinoma.security.provider.ldap');
            $definition->addMethodCall('setServers', [$ldapServers]);
        }

        if (array_key_exists('LexikJWTAuthenticationBundle', $container->getParameterBag()->get('kernel.bundles'))) {
            $this->addDefinition(
                $container,
                'Evrinoma\SecurityBundle\Guard\JWT\AuthenticatorGuard',
                'evrinoma.security.guard.jwt',
                [new Reference('Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface')],
                true
            );
        }
    }
//endregion Public

//region SECTION: Getters/Setters
    public function getAlias()
    {
        return EvrinomaSecurityBundle::SECURITY_BUNDLE;
    }
//endregion Getters/Setters
}