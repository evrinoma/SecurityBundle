<?php

namespace Evrinoma\SecurityBundle\Token\JWT;

interface JwtTokenServiceInterface extends JwtTokenGeneratorInterface, JwtTokenInterface, JwtTokenExpiredInterface, JwtTokenAccessInterface, JwtTokenRefreshInterface
{

}