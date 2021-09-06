<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

use Symfony\Component\HttpFoundation\Cookie;

interface JwtTokenInterface
{
    /**
     * @return Cookie
     */
    public function getAccessTokenCookie(): Cookie;
    /**
     * @return Cookie
     */
    public function getRefreshTokenCookie(): Cookie;

    /**
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * @return string
     */
    public function getRefreshToken(): string;
}