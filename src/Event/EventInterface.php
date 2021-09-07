<?php


namespace Evrinoma\SecurityBundle\Event;


use Symfony\Component\HttpFoundation\Cookie;

interface EventInterface
{
//region SECTION: Public
    public function headerCookies(): array;

    public function responseData(): array;

    public function redirectToUrl(): string;

    public function addCookie(Cookie $cookie): EventInterface;
//endregion Public

//region SECTION: Getters/Setters
    public function setUrl(string $url): EventInterface;

    public function setResponse(array $response): EventInterface;
//endregion Getters/Setters
}