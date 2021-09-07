<?php

namespace Evrinoma\SecurityBundle\Configuration;

final class Form
{
//region SECTION: Fields
    private string $username;
    private string $password;
    private string $csrfToken;
//endregion Fields

//region SECTION: Constructor
    /**
     * Form constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $csrfToken
     */
    public function __construct(string $username, string $password, string $csrfToken)
    {
        $this->username  = $username;
        $this->password  = $password;
        $this->csrfToken = $csrfToken;
    }
//endregion Constructor

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function getUsernamePrefix(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPasswordPrefix(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getCsrfTokenPrefix(): string
    {
        return $this->csrfToken;
    }
//endregion Getters/Setters


}