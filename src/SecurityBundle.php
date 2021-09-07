<?php


namespace Evrinoma\SecurityBundle;

use Evrinoma\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SecurityBundle extends Bundle
{
    public const SECURITY_BUNDLE = 'security';

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new SecurityExtension();
        }
        return $this->extension;
    }
}