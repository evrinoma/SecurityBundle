<?php

namespace Evrinoma\SecurityBundle\Guard\Login;

use Evrinoma\SecurityBundle\Configuration\Configuration;
use Evrinoma\SecurityBundle\Event\AuthenticationFailureEvent;
use Evrinoma\SecurityBundle\Event\AuthenticationSuccessEvent;
use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpSecurity;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AuthenticatorGuard extends AbstractGuardAuthenticator
{

//region SECTION: Fields
    /**
     * @var HttpSecurity
     */
    private HttpSecurity $httpSecurity;

    /**
     * @var TokenStorageInterface|null
     */
    private ?TokenStorageInterface $tokenStorage;

    /**
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $csrfTokenManager;

    /**
     * @var EncoderFactoryInterface
     */
    private EncoderFactoryInterface $encoderFactory;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;
//endregion Fields

//region SECTION: Constructor


    /**
     * @param HttpSecurity                 $httpSecurity
     * @param TokenStorageInterface     $tokenStorage
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param Configuration             $configuration
     * @param EncoderFactoryInterface   $encoderFactory
     */
    public function __construct(HttpSecurity $httpSecurity, TokenStorageInterface $tokenStorage, CsrfTokenManagerInterface $csrfTokenManager, EventDispatcherInterface $eventDispatcher, Configuration $configuration, EncoderFactoryInterface $encoderFactory)
    {
        $this->httpSecurity        = $httpSecurity;
        $this->tokenStorage     = $tokenStorage;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->encoderFactory   = $encoderFactory;
        $this->configuration    = $configuration;
        $this->eventDispatcher  = $eventDispatcher;
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
        return ($request->request->has($this->configuration->form()->getUsernamePrefix()) && $request->request->has($this->configuration->form()->getPasswordPrefix()));
    }

    /**
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = null;

        if ($this->configuration->event()->isAuthenticationFailureEnabled()) {
            $event = new AuthenticationFailureEvent();
            $this->eventDispatcher->dispatch($event);
            $response =new JsonResponse($event->responseData(), Response::HTTP_UNAUTHORIZED);
            foreach ($event->headerCookies() as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        return $response;
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
        $response = null;

        if ($this->httpSecurity->checkRequestPath($request, '/'.$this->configuration->route()->loginCheck())) {
            $redirectUrl = $this->configuration->route()->redirect();

            if ($this->configuration->event()->isAuthenticationSuccessEnabled()) {
                $event = new AuthenticationSuccessEvent();
                $this->eventDispatcher->dispatch($event);
                $redirectUrl = $event->redirectToUrl();
                $response    = new JsonResponse($event->responseData(), Response::HTTP_OK);
                foreach ($event->headerCookies() as $cookie) {
                    $response->headers->setCookie($cookie);
                }
            } else {
                $response = new JsonResponse([], Response::HTTP_NO_CONTENT);
            }

            if ($this->configuration->isRedirectByServer()) {
                $response = $this->httpSecurity->createRedirectResponse($request, $redirectUrl);
            }
        }

        return $response;
    }

    /**
     * @param Request                      $request       The request that resulted in an AuthenticationException
     * @param AuthenticationException|null $authException The exception that started the authentication process
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpSecurity->createRedirectResponse($request, $this->configuration->route()->login());
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
        return ($user && $this->checkUser($user, $credentials->getPassword()));
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
//endregion Public

//region SECTION: Private
    private function checkUser($user, $password)
    {
        $encoder     = $this->encoderFactory->getEncoder($user);
        $encodedPass = $encoder->encodePassword($password, $user->getSalt());

        return $encodedPass === $user->getPassword();
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationExtractor($this->configuration->form());

        $extractor->extract($request);

        if ($extractor->hasCsrfToken() && (false === $this->csrfTokenManager->isTokenValid(new CsrfToken(SecurityModelInterface::AUTHENTICATE, $extractor->getCsrfToken())))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
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