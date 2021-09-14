<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

use Symfony\Component\HttpFoundation\Cookie;

interface JwtTokenRefreshInterface
{
//region SECTION: Getters/Setters
    public function setRefreshToken(string $refreshToken): JwtTokenRefreshInterface;

    public function setRefreshTokenCookie(string $token, int $expire): JwtTokenRefreshInterface;
//endregion Getters/Setters
}