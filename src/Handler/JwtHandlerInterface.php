<?php

namespace Evrinoma\SecurityBundle\Handler;

use Evrinoma\SecurityBundle\Token\JWT\JwtTokenInterface;
use Symfony\Component\HttpFoundation\Request;

interface JwtHandlerInterface
{
//region SECTION: Getters/Setters
    public function setRefreshToken(?string $refreshToken): JwtHandlerInterface;

    public function setAccessToken(?string $accessToken): JwtHandlerInterface;

    public function doCheck(Request $request): JwtTokenInterface;
//endregion Getters/Setters
}