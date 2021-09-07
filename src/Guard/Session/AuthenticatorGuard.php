<?php

namespace Evrinoma\SecurityBundle\Guard\Session;

use Evrinoma\SecurityBundle\Configuration\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AuthenticatorGuard extends AbstractGuardAuthenticator
{
//region SECTION: Fields

    /**
     * @var TokenStorageInterface|null
     */
    private ?TokenStorageInterface $tokenStorage;
    /**
     * @var string
     */
    private string $sessionKey;
//endregion Fields

//region SECTION: Constructor
    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, Configuration $configuration)
    {
        $this->tokenStorage = $tokenStorage;
        $this->sessionKey   = '_security_'.$configuration->getFireWallSessionKey();;
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->hasPreviousSession() && $request->hasSession();
    }

    /**
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData()),];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @param Request                      $request       The request that resulted in an AuthenticationException
     * @param AuthenticationException|null $authException The exception that started the authentication process
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = ['message' => 'Session Authentication Required',];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     *
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationExtractor();

        $extractor->extract($request);

        $session = $request->hasPreviousSession() && $request->hasSession() ? $request->getSession() : null;

        $serializedToken = (null !== $session && $session instanceof Session) ? $session->get($this->sessionKey) : null;

        if (null === $session || null === $serializedToken) {
            throw new SessionUnavailableException();
        }

        $token = null;

        try {
            $token = unserialize($serializedToken);
        } catch (\Throwable $e) {
            throw new CustomUserMessageAuthenticationException('Unserialize error');
        }

        if ($token instanceof TokenInterface) {
            $this->tokenStorage->setToken($token);
        }

        if ($this->tokenStorage->getToken()) {
            $extractor->setUserName($this->tokenStorage->getToken()->getUser()->getUserName());
        }

        return $extractor;
    }

    /**
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     * @throws AuthenticationException
     *
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials->getUserName());
    }
//endregion Getters/Setters
}