<?php


namespace Evrinoma\SecurityBundle\AccessControl;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractAccessControl implements AccessControlInterface
{
    /**
     * @var Security
     */
    private Security $security;

//region SECTION: Constructor

    /**
     * AccessControl constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
//endregion Constructor

//region SECTION: Getters/Setters
    public function getAuthorizedUser(): UserInterface
    {
        return $this->security->getUser();
    }

    public function isAuthorize(): bool
    {
        return (($this->security->getToken() !== null) && (!$this->security->getToken() instanceof AnonymousToken));
    }
//endregion Getters/Setters
}