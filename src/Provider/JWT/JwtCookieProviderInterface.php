<?php

namespace Evrinoma\SecurityBundle\Provider\JWT;

use Symfony\Component\HttpFoundation\Cookie;

interface JwtCookieProviderInterface
{
    public function createCookie(string $jwt, ?string $name = null, $expiresAt = null, ?string $sameSite = null, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httpOnly = null, array $split = []): Cookie;
}