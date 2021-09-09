<?php


namespace Evrinoma\SecurityBundle\Event;


use Symfony\Component\HttpFoundation\Cookie;

abstract class AbstractAuthenticationEvent implements EventInterface
{
//region SECTION: Fields
    /**
     * @var string
     */
    private string $url = '';
    /**
     * @var array
     */
    private array $response = [];
    /**
     * @var array
     */
    private array $headers = [];
    /**
     * @var Cookie[]
     */
    private array $cookies = [];
//endregion Fields

//region SECTION: Public
    public function headerCookies(): array
    {
        return $this->cookies;
    }

    public function headerData(): array
    {
        return $this->headers;
    }

    public function responseData(): array
    {
        return $this->response;
    }

    public function redirectToUrl(): string
    {
        return $this->url;
    }

    public function addCookie(Cookie $cookie): EventInterface
    {
        $this->cookies[] = $cookie;

        return $this;
    }

    public function addHeader(string $key, string $value): EventInterface
    {
        $this->headers[$key] = $value;

        return $this;
    }
//endregion Public

//region SECTION: Getters/Setters
    public function setUrl(string $url): EventInterface
    {
        $this->url = $url;

        return $this;
    }

    public function setResponse(array $response): EventInterface
    {
        $this->response = $response;

        return $this;
    }
//endregion Getters/Setters
}