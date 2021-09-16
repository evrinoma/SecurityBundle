<?php

namespace Evrinoma\SecurityBundle\Handler;

use Evrinoma\SecurityBundle\AccessControl\AccessControlInterface;
use Evrinoma\SecurityBundle\Configuration\Configuration;
use Evrinoma\SecurityBundle\Model\SecurityModelInterface;
use Evrinoma\SecurityBundle\Token\JWT\JwtTokenGeneratorInterface;
use Evrinoma\SecurityBundle\Token\JWT\JwtTokenInterface;
use Evrinoma\SecurityBundle\Token\JWT\JwtTokenServiceInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\HttpUtils;

final class JwtHandler implements JwtHandlerInterface
{
//region SECTION: Fields
    private Configuration            $configuration;
    private AccessControlInterface   $accessControl;
    private HttpUtils                $httpUtils;
    private JwtTokenServiceInterface $jwtTokenGenerator;
    private JWTTokenManagerInterface $jwtManager;
    private ?string                  $refreshToken = null;
    private ?string                  $accessToken  = null;
//endregion Fields

//region SECTION: Constructor
    /**
     * @param HttpUtils                $httpUtils
     * @param JWTTokenManagerInterface $jwtManager
     * @param JwtTokenServiceInterface $jwtTokenGenerator
     * @param AccessControlInterface   $accessControl
     */
    public function __construct(HttpUtils $httpUtils, JWTTokenManagerInterface $jwtManager, JwtTokenServiceInterface $jwtTokenGenerator, AccessControlInterface $accessControl, Configuration $configuration)
    {
        $this->jwtManager        = $jwtManager;
        $this->jwtTokenGenerator = $jwtTokenGenerator;
        $this->httpUtils         = $httpUtils;
        $this->accessControl     = $accessControl;
        $this->configuration     = $configuration;
    }
//endregion Constructor


//region SECTION: Public
    public function doCheck(Request $request): JwtTokenInterface
    {
        $this->jwtTokenGenerator->reset();

        if ($this->accessControl->isAuthorize()
            && !$this->httpUtils->checkRequestPath($request, '/'.$this->configuration->route()->loginCheck())
            && !$this->httpUtils->checkRequestPath($request, '/'.$this->configuration->route()->login())
            && !($request->request->has(SecurityModelInterface::AUTHENTICATE) && $request->request->get(SecurityModelInterface::AUTHENTICATE) === SecurityModelInterface::BEARER)
        ) {
            if ($this->refreshToken) {
                try {
                    $user= $this->doCheckUser($this->accessToken);
                    try {
                        $payload = $this->getPayload($this->refreshToken);
                    } catch (\Exception $e) {
                        throw new InvalidTokenException('Wrong refresh token');
                    }
                    $this->jwtTokenGenerator->generate($user);

                    $this->jwtTokenGenerator
                        ->refresh()
                        ->setRefreshToken($this->refreshToken)
                        ->setRefreshTokenCookie( $this->refreshToken, $payload[JwtTokenGeneratorInterface::EXP_KEY]);
                } catch (MissingTokenException | JWTDecodeFailureException $e) {
                    if ($e instanceof MissingTokenException || ($e instanceof JWTDecodeFailureException && JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason())) {
                        try {
                            $user = $this->doCheckUser($this->refreshToken);
                            $this->jwtTokenGenerator->generate($user);
                        } catch (\Exception $e) {
                            throw new InvalidTokenException('Can\'t restore refresh token');
                        }
                    }
                } catch (\Exception $e) {
                    throw new InvalidTokenException('Invalid JWT Token');
                }
            } else {
                //перенаправление на страницу логин если пользователь есть но нет refresh токена
                throw new InvalidTokenException('Invalid JWT Refresh Token');
            }
        }

        return $this->jwtTokenGenerator;
    }
//endregion Public

//region SECTION: Private
    /**
     * @param string|null $token
     *
     * @return array
     * @throws InvalidTokenException
     * @throws MissingTokenException
     */
    private function getPayload(?string $token): array
    {
        if ($token === null) {
            throw new MissingTokenException('Missing JWT Token');
        } else {
            $preAccessToken = new PreAuthenticationJWTUserToken($token);
        }
        if (!$payload = $this->jwtManager->decode($preAccessToken)) {
            throw new InvalidTokenException('Invalid JWT Decode Token');
        }

        return $payload;
    }

    /**
     * @param string|null $token
     *
     * @return UserInterface
     * @throws InvalidTokenException
     * @throws MissingTokenException
     */
    private function doCheckUser(?string $token): UserInterface
    {
        $payload = $this->getPayload($token);
        $user    = $this->accessControl->getAuthorizedUser();
        if ($payload[JwtTokenGeneratorInterface::PAYLOAD_KEY] !== $user->getUserName()) {
            throw new InvalidTokenException('Invalid Payload JWT Token');
        }

        return $user;
    }
//endregion Private

//region SECTION: Getters/Setters
    public function setRefreshToken(?string $refreshToken): JwtHandlerInterface
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function setAccessToken(?string $accessToken): JwtHandlerInterface
    {
        $this->accessToken = $accessToken;

        return $this;
    }
//endregion Getters/Setters
}

