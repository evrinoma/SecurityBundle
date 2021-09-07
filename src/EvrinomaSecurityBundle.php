<?php


namespace Evrinoma\SecurityBundle;

use Evrinoma\SecurityBundle\DependencyInjection\EvrinomaSecurityExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EvrinomaSecurityBundle extends Bundle
{
    public const SECURITY_BUNDLE = 'safety';

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new EvrinomaSecurityExtension();
        }
        return $this->extension;
    }
}