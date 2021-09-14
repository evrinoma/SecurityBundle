<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

interface JwtTokenGeneratorInterface
{
//region SECTION: Fields
    public const PAYLOAD_KEY = 'payload';
    public const EXP_KEY     = 'exp';
//endregion Fields

//region SECTION: Public
    /**
     * @param UserInterface $user
     *
     * @return JwtTokenGeneratorInterface
     */
    public function generate(UserInterface $user): JwtTokenGeneratorInterface;

    public function reset(): JwtTokenGeneratorInterface;
//endregion Public
}