<?php


namespace Evrinoma\SecurityBundle\AccessControl;

use Symfony\Component\Security\Core\User\UserInterface;

interface AccessControlInterface
{
//region SECTION: Getters/Setters
    public function getAuthorizedUser(): UserInterface;

    public function isAuthorize(): bool;

    public function denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.'): void;
//endregion Getters/Setters
}