<?php

namespace Evrinoma\SecurityBundle\Provider\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Cookie\JWTCookieProvider;
use Symfony\Component\HttpFoundation\Cookie;

final class JwtCookieProviderAdaptor implements JwtCookieProviderInterface
{
//region SECTION: Fields
    /**
     * @var JWTCookieProvider
     */
    private JWTCookieProvider $cookieProvider;
//endregion Fields

//region SECTION: Constructor
    public function __construct(JWTCookieProvider $cookieProvider)
    {
        $this->cookieProvider = $cookieProvider;
    }
//endregion Constructor

//region SECTION: Public
    public function createCookie(string $jwt, ?string $name = null, $expiresAt = null, ?string $sameSite = null, ?string $path = null, ?string $domain = null, ?bool $secure = null, ?bool $httpOnly = null, array $split = []): Cookie
    {
        return $this->cookieProvider->createCookie($jwt, $name, $expiresAt, $sameSite, $path, $domain, $secure, $httpOnly, $split);
    }
//endregion Public
}