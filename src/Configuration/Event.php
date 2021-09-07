<?php

namespace Evrinoma\SecurityBundle\Configuration;

final class Event
{
//region SECTION: Fields
    private bool $authenticationSuccess;
    private bool $authenticationFailure;
//endregion Fields

//region SECTION: Constructor
    /**
     * Event constructor.
     *
     * @param bool $authenticationSuccess
     * @param bool $authenticationFailure
     */
    public function __construct(bool $authenticationSuccess, bool $authenticationFailure)
    {
        $this->authenticationSuccess = $authenticationSuccess;
        $this->authenticationFailure = $authenticationFailure;
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @return bool
     */
    public function isAuthenticationSuccessEnabled(): bool
    {
        return $this->authenticationSuccess;
    }

    /**
     * @return bool
     */
    public function isAuthenticationFailureEnabled(): bool
    {
        return $this->authenticationFailure;
    }
//endregion Public
}