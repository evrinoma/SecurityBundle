<?php

namespace Evrinoma\SecurityBundl\Token\JWT;

use Symfony\Component\Security\Core\User\UserInterface;

interface JwtTokenGeneratorInterface
{
    public const PAYLOAD_KEY = 'payload';
    public const EXP_KEY = 'exp';
    /**
     * @param UserInterface $user
     *
     * @return JwtTokenGeneratorInterface
     */
    public function generate(UserInterface $user): JwtTokenGeneratorInterface;
}