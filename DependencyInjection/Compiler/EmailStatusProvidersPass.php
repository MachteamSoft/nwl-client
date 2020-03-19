<?php

namespace Mach\Bundle\NwlBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EmailStatusProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('nwl.email_status_provider');

        foreach ($container->findTaggedServiceIds('email_status.provider') as $id => $attrs) {
            $provider = new Reference($id);
            $definition->addMethodCall('addProvider', array($provider));
        }
    }
}