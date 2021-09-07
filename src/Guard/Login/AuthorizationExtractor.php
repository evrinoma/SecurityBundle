<?php


namespace Evrinoma\SecurityBundle\Guard\Login;

use Evrinoma\SecurityBundle\Configuration\Form;
use Evrinoma\SecurityBundle\Guard\ExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

final class AuthorizationExtractor implements ExtractorInterface
{
//region SECTION: Fields
    //region SECTION: Fields
    /**
     * @var string|null
     */
    private ?string $username = null;
    /**
     * @var string|null
     */
    private ?string $password = null;
    /**
     * @var string|null
     */
    private ?string $csrfToken = null;
    /**
     * @var Form
     */
    private Form $form;
//endregion Fields

//region SECTION: Constructor
    /**
     * AuthorizationExtractor constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }
//endregion Constructor

//region SECTION: Public
    public function extract(Request $request): void
    {
        if ($request->request->has($this->form->getUsernamePrefix())) {
            $this->setUserName(trim($request->request->get($this->form->getUsernamePrefix())));
        }
        if ($request->request->has($this->form->getPasswordPrefix())) {
            $this->setPassword($request->request->get($this->form->getPasswordPrefix()));
        }
        if ($request->request->has($this->form->getCsrfTokenPrefix())) {
            $this->setCsrfToken($request->request->get($this->form->getCsrfTokenPrefix()));
        }
    }

    /**
     * @return bool
     */
    public function hasCsrfToken(): bool
    {
        return null !== $this->csrfToken;
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUserName(string $username): AuthorizationExtractor
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): AuthorizationExtractor
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $csrfToken
     *
     * @return $this
     */
    public function setCsrfToken(string $csrfToken): AuthorizationExtractor
    {
        $this->csrfToken = $csrfToken;

        return $this;
    }
//endregion Getters/Setters
}