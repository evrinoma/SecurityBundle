<?php


namespace Evrinoma\SecurityBundle\Guard\Session;

use Evrinoma\SecurityBundle\Guard\ExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

final class AuthorizationExtractor implements ExtractorInterface
{
//region SECTION: Fields
    /**
     * @var string|null
     */
    private ?string $username = null;
//endregion Fields

//region SECTION: Public
    public function extract(Request $request): void
    {

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
     * @param string $username
     *
     * @return AuthorizationExtractor
     */
    public function setUsername(string $username): AuthorizationExtractor
    {
        $this->username = $username;

        return $this;
    }
//endregion Getters/Setters

}