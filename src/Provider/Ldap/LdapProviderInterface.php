<?php


namespace Evrinoma\SecurityBundle\Provider\Ldap;

use Evrinoma\SecurityBundle\Provider\ProviderInterface;

interface LdapProviderInterface extends ProviderInterface
{
    /**
     * @param string      $userName
     * @param string      $password
     * @param string|null $domain
     *
     * @return bool
     */
    public function checkUser(string $userName, string $password, string $domain = null): bool;

    /**
     * @param array $servers
     */
    public function setServers(array $servers):void;
}