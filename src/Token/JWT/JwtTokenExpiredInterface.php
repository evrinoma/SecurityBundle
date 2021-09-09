<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

use Symfony\Component\HttpFoundation\Cookie;

interface JwtTokenExpiredInterface
{
//region SECTION: Getters/Setters
    public function getExpiredRefreshTokenCookie(): Cookie;

    public function getExpiredAccessTokenCookie(): Cookie;
//endregion Getters/Setters
}