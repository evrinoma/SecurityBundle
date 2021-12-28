<?php


namespace Evrinoma\SecurityBundle\AccessControl;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractAccessControl implements AccessControlInterface
{
//region SECTION: Fields
    /**
     * @var Security
     */
    private Security $security;
//endregion Fields

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

//region SECTION: Protected
    /**
     * Returns an AccessDeniedException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedException('Unable to access this page!');
     *
     * @throws \LogicException If the Security component is not available
     */
    protected function createAccessDeniedException(string $message = 'Access Denied.', \Throwable $previous = null): AccessDeniedException
    {
        if (!class_exists(AccessDeniedException::class)) {
            throw new \LogicException('You can not use the "createAccessDeniedException" method if the Security component is not available. Try running "composer require symfony/security-bundle".');
        }

        return new AccessDeniedException($message, $previous);
    }
//endregion Protected

//region SECTION: Public
    public function isAuthorize(): bool
    {
        return (($this->security->getToken() !== null) && (!$this->security->getToken() instanceof AnonymousToken));
    }

    /**
     * Throws an exception unless the attribute is granted against the current authentication token and optionally
     * supplied subject.
     *
     * @throws AccessDeniedException
     */
    public function denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->security->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }
//endregion Public

//region SECTION: Getters/Setters
    public function getAuthorizedUser(): UserInterface
    {
        return $this->security->getUser();
    }
//endregion Getters/Setters
}