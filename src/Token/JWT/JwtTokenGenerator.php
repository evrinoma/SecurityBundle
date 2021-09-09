<?php


namespace Evrinoma\SecurityBundle\Token\JWT;


use Evrinoma\SecurityBundle\Provider\JWT\JwtCookieProviderInterface;
use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtTokenGenerator implements JwtTokenServiceInterface
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
     * @var Cookie
     */
    private ?Cookie $refreshTokenCookie = null;
    /**
     * @var Cookie
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

    private int $time;

//endregion Fields

//region SECTION: Constructor
    /**
     * @param JWTEncoderInterface $encoder
     * @param JwtCookieProviderInterface   $cookieProvider
     * @param string              $domain
     * @param int                 $jwtRefreshTokenTtl
     * @param int                 $jwtAccessTokenTtl
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
     * @param UserInterface $user
     *
     * @return $this
     */
    public function generate(UserInterface $user): JwtTokenGenerator
    {
        $this
            ->setUser($user)
            ->generateAccessToken()
            ->generateRefreshToken()
            ->generateAccessTokenCookie()
            ->generateRefreshTokenCookie();

        return $this;
    }
//endregion Public

//region SECTION: Private
    private function setUser(UserInterface $user): JwtTokenGenerator
    {
        $this->user = $user;

        return $this;
    }

    private function generateAccessToken(): JwtTokenGenerator
    {
        $this->accessToken = $this->encoder->encode([JwtTokenGeneratorInterface::PAYLOAD_KEY => $this->user->getUsername(), JwtTokenGeneratorInterface::EXP_KEY => $this->time + $this->accessTokenTtl]);

        return $this;
    }

    private function generateRefreshToken(): JwtTokenGenerator
    {
        $this->refreshToken = $this->encoder->encode([JwtTokenGeneratorInterface::PAYLOAD_KEY => $this->user->getUsername(), JwtTokenGeneratorInterface::EXP_KEY => $this->time + $this->refreshTokenTtl]);

        return $this;
    }

    private function generateRefreshTokenCookie(): JwtTokenGenerator
    {
        $this->refreshTokenCookie = $this->cookieProvider->createCookie($this->refreshToken, SecurityModelInterface::REFRESH, $this->time + $this->refreshTokenTtl, 'lax', '/', $this->domain, false, true);

        return $this;
    }

    private function generateAccessTokenCookie(): JwtTokenGenerator
    {
        $this->accessTokenCookie = $this->cookieProvider->createCookie($this->accessToken, SecurityModelInterface::BEARER, $this->time + $this->refreshTokenTtl, 'lax', '/', $this->domain, false, true);

        return $this;
    }
//endregion Private

//region SECTION: Getters/Setters
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
//endregion Getters/Setters
}