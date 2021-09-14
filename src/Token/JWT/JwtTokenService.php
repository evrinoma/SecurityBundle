<?php


namespace Evrinoma\SecurityBundle\Token\JWT;


use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Evrinoma\SecurityBundle\Provider\JWT\JwtCookieProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtTokenService implements JwtTokenServiceInterface
{
//region SECTION: Fields
    /**
     * @var JwtCookieProviderInterface
     */
    private JwtCookieProviderInterface $cookieProvider;
    /**
     * @var JWTEncoderInterface
     */
    private JWTEncoderInterface $encoder;
    /**
     * @var int
     */
    private int $accessTokenTtl;
    /**
     * @var int
     */
    private int $refreshTokenTtl;
    /**
     * @var int
     */
    private string $domain;
    /**
     * @var Cookie|null
     */
    private ?Cookie $refreshTokenCookie = null;
    /**
     * @var Cookie|null
     */
    private ?Cookie $accessTokenCookie = null;
    /**
     * @var string
     */
    private string $refreshToken;
    /**
     * @var string
     */
    private string $accessToken;
    /**
     * @var UserInterface
     */
    private UserInterface $user;
    /**
     * @var int
     */
    private int $time;
    /**
     * @var bool
     */
    private bool $valid = false;
//endregion Fields

//region SECTION: Constructor
    /**
     * @param JWTEncoderInterface        $encoder
     * @param JwtCookieProviderInterface $cookieProvider
     * @param string                     $domain
     * @param int                        $jwtRefreshTokenTtl
     * @param int                        $jwtAccessTokenTtl
     */
    public function __construct(JWTEncoderInterface $encoder, JwtCookieProviderInterface $cookieProvider, string $domain, int $jwtAccessTokenTtl, int $jwtRefreshTokenTtl)
    {
        $this->encoder         = $encoder;
        $this->cookieProvider  = $cookieProvider;
        $this->domain          = $domain;
        $this->refreshTokenTtl = $jwtRefreshTokenTtl;
        $this->accessTokenTtl  = $jwtAccessTokenTtl;
        $this->time            = time();
    }

//region SECTION: Public

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    public function reset(): JwtTokenServiceInterface
    {
        $this->setValid(false);

        return $this;
    }

    /**
     * @param UserInterface $user
     *
     * @return $this
     */
    public function generate(UserInterface $user): JwtTokenServiceInterface
    {
        $this
            ->setUser($user)
            ->generateAccessToken()
            ->generateRefreshToken()
            ->generateAccessTokenCookie()
            ->generateRefreshTokenCookie();

        $this->setValid();

        return $this;
    }

    public function expired(): JwtTokenExpiredInterface
    {
        return $this;
    }

    public function refresh(): JwtTokenRefreshInterface
    {
        return $this;
    }
//endregion Public

//region SECTION: Private
    /**
     * @param bool $valid
     *
     * @return JwtTokenService
     */
    private function setValid(bool $valid = true): JwtTokenService
    {
        $this->valid = $valid;

        return $this;
    }

    private function setUser(UserInterface $user): JwtTokenService
    {
        $this->user = $user;

        return $this;
    }

    private function generateAccessToken(): JwtTokenService
    {
        $this->accessToken = $this->encoder->encode([JwtTokenGeneratorInterface::PAYLOAD_KEY => $this->user->getUsername(), JwtTokenGeneratorInterface::EXP_KEY => $this->time + $this->accessTokenTtl]);

        return $this;
    }

    private function generateRefreshToken(): JwtTokenService
    {
        $this->refreshToken = $this->encoder->encode([JwtTokenGeneratorInterface::PAYLOAD_KEY => $this->user->getUsername(), JwtTokenGeneratorInterface::EXP_KEY => $this->time + $this->refreshTokenTtl]);

        return $this;
    }

    private function generateRefreshTokenCookie(): JwtTokenService
    {
        $this->refreshTokenCookie = $this->createCookie($this->refreshToken, SecurityModelInterface::REFRESH, $this->time + $this->refreshTokenTtl);

        return $this;
    }

    private function generateAccessTokenCookie(): JwtTokenService
    {
        $this->accessTokenCookie = $this->createCookie($this->accessToken, SecurityModelInterface::BEARER, $this->time + $this->accessTokenTtl);

        return $this;
    }

    private function createCookie(string $payload, string $key, int $expire = 0, bool $jwtCookie = true): Cookie
    {
        return $jwtCookie ?
            $this->cookieProvider->createCookie($payload, $key, $expire, Cookie::SAMESITE_LAX, '/', $this->domain, false, true)
            : new Cookie($key, $payload, $expire, '/', $this->domain, false, true, Cookie::SAMESITE_LAX);
    }
//endregion Private

//region SECTION: Getters/Setters
    public function getExpiredRefreshTokenCookie(): Cookie
    {
        return $this->createCookie("", SecurityModelInterface::REFRESH, 0, false);
    }

    public function getExpiredAccessTokenCookie(): Cookie
    {
        return $this->createCookie("", SecurityModelInterface::BEARER, 0, false);
    }

    /**
     * @return Cookie
     */
    public function getRefreshTokenCookie(): Cookie
    {
        return $this->refreshTokenCookie;
    }

    /**
     * @return Cookie
     */
    public function getAccessTokenCookie(): Cookie
    {
        return $this->accessTokenCookie;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): JwtTokenRefreshInterface
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function setRefreshTokenCookie(string $token, int $expire): JwtTokenRefreshInterface
    {
        $this->refreshTokenCookie = $this->createCookie($token, SecurityModelInterface::REFRESH, $expire);

        return $this;
    }

    public function setAccessToken(string $accessToken): JwtTokenRefreshInterface
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function setAccessTokenCookie(string $token, int $expire): JwtTokenRefreshInterface
    {
        $this->accessTokenCookie = $this->createCookie($token, SecurityModelInterface::BEARER, $expire);

        return $this;
    }
//endregion Getters/Setters

}