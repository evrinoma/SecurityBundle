<?php

namespace Evrinoma\SecurityBundle\Configuration;

final class Route
{
//region SECTION: Fields
    private string $login;
    private string $loginCheck;
    private string $redirect;
//endregion Fields

//region SECTION: Constructor
    /**
     * Route constructor.
     *
     * @param string $login
     * @param string $loginCheck
     * @param string $redirect
     */
    public function __construct(string $login, string $loginCheck, string $redirect)
    {
        $this->login      = $login;
        $this->loginCheck = $loginCheck;
        $this->redirect   = $redirect;
    }
//endregion Constructor

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function login(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function loginCheck(): string
    {
        return $this->loginCheck;
    }

    /**
     * @return string
     */
    public function redirect(): string
    {
        return $this->redirect;
    }
//endregion Getters/Setters

}