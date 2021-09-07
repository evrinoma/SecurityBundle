<?php


namespace Evrinoma\SecurityBundle\Configuration;

final class Configuration
{
//region SECTION: Fields
    private Event $event;
    private Form  $form;
    private Route $route;

    private bool $redirectByServer;

    private string $fireWallSessionKey;

//endregion Fields

//region SECTION: Constructor
    /**
     * Configuration constructor.
     *
     * @param bool   $authenticationSuccessEvent
     * @param bool   $authenticationFailureEvent
     * @param bool   $redirectByServer
     * @param string $fireWallSessionKey
     * @param string $login
     * @param string $loginCheck
     * @param string $redirect
     * @param string $username
     * @param string $password
     * @param string $csrfToken
     */
    public function __construct(bool $authenticationSuccessEvent, bool $authenticationFailureEvent, bool $redirectByServer, string $fireWallSessionKey, string $login, string $loginCheck, string $redirect, string $username, string $password, string $csrfToken)
    {
        $this->redirectByServer   = $redirectByServer;
        $this->fireWallSessionKey = $fireWallSessionKey;

        $this->event = new Event($authenticationSuccessEvent, $authenticationFailureEvent);
        $this->form  = new Form($username, $password, $csrfToken);
        $this->route = new Route($login, $loginCheck, $redirect);
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @return bool
     */
    public function isRedirectByServer(): bool
    {
        return $this->redirectByServer;
    }

    /**
     * @return Event
     */
    public function event(): Event
    {
        return $this->event;
    }

    /**
     * @return Form
     */
    public function form(): Form
    {
        return $this->form;
    }

    /**
     * @return Route
     */
    public function route(): Route
    {
        return $this->route;
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function getFireWallSessionKey(): string
    {
        return $this->fireWallSessionKey;
    }
//endregion Getters/Setters
}