<?php


namespace Evrinoma\SecurityBundle\Provider\Ldap;

class LdapProvider implements LdapProviderInterface
{
//region SECTION: Fields
    /**
     * @var mixed $connect ldap server connector
     */
    private $connect = false;

    /**
     * @var array $servers
     */
    private array $servers;

    /**
     * @var string
     */
    private string $domain;
//endregion Fields

//region SECTION: Public
    /**
     * @param string      $userName
     * @param string      $password
     * @param string|null $domain
     *
     * @return bool
     */
    public function checkUser(string $userName, string $password, string $domain = null): bool
    {
        //нет пароля не нужно лезть в ldap
        //создать подключение и проверяем пользователя
        return ($password !== '' && $this->isSetServers()) ? $this->searchLdap($userName, $password, $domain) : false;
    }

//region SECTION: Private
    /**
     * сбрасываем соединение
     */
    private function closeLdap()
    {
        if ($this->connect) {
            ldap_unbind($this->connect);
        }
    }

    /**
     * @param string      $userName
     * @param string      $password
     * @param string|null $domain
     *
     * @return bool
     */
    private function bindUser(string $userName, string $password, string $domain): bool
    {
        try {
            $bind = @ldap_bind($this->connect, $userName.'@'.$domain, $password);
        } catch (\ErrorException $e) {
            $bind = false;
        }

        return $bind;
    }

    private function isSetServers(): bool
    {
        return $this->servers !== null && is_array($this->servers);
    }

    /**
     * @param string $userName
     * @param string $password
     * @param array  $servers
     *
     * @return bool
     */
    private function openLdapServer(string $userName, string $password, $servers): bool
    {
        foreach ($servers as $server) {
            $this->connect = ldap_connect($server['host'], $server['port']);
            if (false !== $this->connect) {
                $this->setLdapDefaultSettings();
                if (!$this->bindUser($userName, $password, $this->domain)) {
                    $this->closeLdap();
                } else {
                    $this->closeLdap();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $userName user name
     * @param string $password user pass
     * @param string $domain   domain name
     *
     * @return mixed|resource
     */
    private function searchLdap($userName, $password, $domain): bool
    {
        if (null !== $domain && array_key_exists($domain, $this->servers)) {
            $this->domain = $domain;
            $servers      = $this->servers[$domain];

            return $this->openLdapServer($userName, $password, $servers);
        }

        foreach ($this->servers as $nameDomain => $servers) {
            $this->domain = $nameDomain;
            if ($this->openLdapServer($userName, $password, $servers)) {
                return true;
            }
        }

        $this->domain = '';

        return false;
    }

    /**
     * настроки поумолчанию для ldap серверов
     */
    private function setLdapDefaultSettings()
    {
        //for win2003
        ldap_set_option($this->connect, LDAP_OPT_PROTOCOL_VERSION, 3) or die("Could set protocol version 3");
        //for win2003
        ldap_set_option($this->connect, LDAP_OPT_REFERRALS, 0) or die("Could not set referrals");
    }
//endregion Private

    /**
     * @param array $servers
     */
    public function setServers(array $servers): void
    {
        $this->servers = $servers;
    }
}