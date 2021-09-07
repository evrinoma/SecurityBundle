<?php

namespace Evrinoma\SecurityBundle\Guard\JWT;

use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AuthenticatorGuard extends AbstractGuardAuthenticator
{
//region SECTION: Fields
    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;
//endregion Fields

//region SECTION: Constructor
    /**
     * AuthenticatorGuard constructor.
     *
     * @param JWTTokenManagerInterface $jwtManager
     */
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
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
        return $request->headers->has(SecurityModelInterface::BEARER);
    }

    /**
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

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
        $data = [
            'message' => 'JWTAccessToken Authentication Required',
        ];

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
        $extractor = new AuthorizationExtractor(SecurityModelInterface::BEARER);

        $extractor->extract($request);

        $jwtAccessToken = $extractor->getJwtAccessToken();

        if ($jwtAccessToken === null) {
            throw new AuthenticationException('Missing JWT Access Token');
        } else {
            $preAccessToken = new PreAuthenticationJWTUserToken($jwtAccessToken);
        }
        try {
            if (!$payload = $this->jwtManager->decode($preAccessToken)) {
                throw new AuthenticationException('Invalid JWT Decode Access Token');
            }
        } catch (JWTDecodeFailureException $e) {
            throw new AuthenticationException($e->getMessage());
        }

        $extractor->setUsername($payload['username']);

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