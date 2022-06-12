<?php


namespace Evrinoma\SecurityBundle\Event;


use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

interface EventInterface
{
//region SECTION: Public
    public function headerCookies(): array;

    public function headerData(): array;

    public function responseData(): array;

    public function redirectToUrl(): string;

    public function addCookie(Cookie $cookie): EventInterface;

    public function addHeader(string $key, string $value): EventInterface;
//endregion Public

//region SECTION: Getters/Setters
    public function setUrl(string $url): EventInterface;

    public function setResponse(array $response): EventInterface;

    public function setUser(UserInterface $user): EventInterface;
//endregion Getters/Setters
}