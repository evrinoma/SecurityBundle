<?php

namespace Evrinoma\SecurityBundle\Token\JWT;


interface JwtTokenAccessInterface
{
//region SECTION: Getters/Setters
    public function setAccessToken(string $accessToken): JwtTokenRefreshInterface;

    public function setAccessTokenCookie(string $token, int $expire): JwtTokenRefreshInterface;
//endregion Getters/Setters
}