<?php


namespace Evrinoma\SecurityBundle\Voter;

interface RoleInterface
{
//region SECTION: Fields
    public const ROLE_SUPER_ADMIN  = 'ROLE_SUPER_ADMIN';
    public const ROLE_USER         = 'ROLE_USER';
    public const ROLE_NO_LDAP_USER = 'ROLE_NO_LDAP_USER';
    public const ROLE_API          = 'ROLE_API';
    public const ROLE_DEV_USER     = 'ROLE_DEV_USER';
//endregion Fields
}