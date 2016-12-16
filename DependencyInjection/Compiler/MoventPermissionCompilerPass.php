<?php


namespace Movent\PermissionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class MoventPermissionCompilerPass implements CompilerPassInterface
{  
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    { 
        if (!$container->hasDefinition('movent_permission.treebuilder')){ 
            return;
        }
		
        $definition 	= $container->getDefinition('movent_permission.treebuilder'); 
		$definition->addArgument($container->getParameter('movent_permission.jstree'));
		
        $taggedServices = $container->findTaggedServiceIds('movent.permission'); 
		 
		foreach ($taggedServices as $id => $tagAttributes){
			$reference = new Reference($id);
            foreach ($tagAttributes as $attributes) { 
                $definition->addMethodCall(
                    'addModule',
                    array($attributes['label'],$reference,$container->getParameter('movent_permission.modules'))
                );
            }  
        }  
    }
}