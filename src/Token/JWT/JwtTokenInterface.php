<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

use Symfony\Component\HttpFoundation\Cookie;

interface JwtTokenInterface
{
//region SECTION: Public
    /**
     * @return bool
     */
    public function isValid(): bool;
//endregion Public

//region SECTION: Getters/Setters
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

    /**
     * @return JwtTokenExpiredInterface
     */
    public function expired(): JwtTokenExpiredInterface;

    /**
     * @return JwtTokenRefreshInterface
     */
    public function refresh(): JwtTokenRefreshInterface;
//endregion Getters/Setters
}