<?php

namespace Mach\Bundle\NwlBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InterceptorProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('nwl.mail_interceptors.chain');

        foreach ($container->findTaggedServiceIds('nwl.interceptor') as $id => $attrs) {

            $priority = $attrs[0]['priority'];
            if (!isset($priority)) {
                throw new \LogicException("Interceptor priority must be set for service id: {$id}");
            }
            $interceptor = new Reference($id);
            $definition->addMethodCall('addInterceptor', array($interceptor, $priority));
        }
    }
}