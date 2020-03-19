<?php

namespace Mach\Bundle\NwlBundle;

use Mach\Bundle\NwlBundle\DependencyInjection\Compiler\EmailStatusProvidersPass;
use Mach\Bundle\NwlBundle\DependencyInjection\Compiler\InterceptorProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MachNwlBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EmailStatusProvidersPass());
        $container->addCompilerPass(new InterceptorProviderPass());
    }
}
