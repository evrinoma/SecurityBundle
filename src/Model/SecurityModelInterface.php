<?php


namespace Evrinoma\SecurityBundle\Model;

interface SecurityModelInterface
{
//region SECTION: Fields
    public const AUTHENTICATE  = 'authenticate';
    public const AUTHORIZATION = 'Authorization';
    public const BEARER        = 'BEARER';
    public const REFRESH       = 'REFRESH';
//endregion Fields
}