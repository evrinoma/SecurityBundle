<?php


namespace Evrinoma\SecurityBundle\Guard\JWT;


use Evrinoma\SecurityBundle\Guard\ExtractorInterface;
use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Symfony\Component\HttpFoundation\Request;

final class AuthorizationExtractor implements ExtractorInterface
{
//region SECTION: Fields
    /**
     * @var string|null
     */
    private ?string $jwtAccessToken = null;
    /**
     * @var string|null
     */
    private ?string $jwtAccessTokenKey = null;
    /**
     * @var string|null
     */
    private ?string $username;
//endregion Fields

//region SECTION: Constructor
    /**
     * AuthorizationExtractor constructor.
     *
     * @param string $jwtAccessTokenKey
     */
    public function __construct(string $jwtAccessTokenKey)
    {
        $this->jwtAccessTokenKey = $jwtAccessTokenKey;
    }
//endregion Constructor


//region SECTION: Public
    public function extract(Request $request): void
    {
        if ($request->headers->has($this->jwtAccessTokenKey)) {
            if (preg_match('/'.ucfirst(mb_strtolower(SecurityModelInterface::BEARER)).'\s(\S+)/', $request->headers->get($this->jwtAccessTokenKey), $matches)) {
                $this->setJwtAccessToken(trim($matches[1]));
            }
        }
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getJwtAccessToken(): ?string
    {
        return $this->jwtAccessToken;
    }

    /**
     * @param string $username
     *
     * @return AuthorizationExtractor
     */
    public function setUsername(string $username): AuthorizationExtractor
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param mixed $jwtAccessToken
     *
     * @return AuthorizationExtractor
     */
    public function setJwtAccessToken($jwtAccessToken): AuthorizationExtractor
    {
        $this->jwtAccessToken = $jwtAccessToken;

        return $this;
    }
//endregion Getters/Setters
}