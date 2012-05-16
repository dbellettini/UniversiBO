<?php
namespace UniversiBO\Bundle\LegacyBundle\Auth;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\DefinitionDecorator;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class UniversiBOFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.legacy.'.$id;
        $container
        ->setDefinition($providerId, new DefinitionDecorator('universibo_legacy.security.authentication.provider'))
        ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.legacy.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('universibo_legacy.security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'legacy';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}

