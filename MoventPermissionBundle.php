<?php

namespace Movent\PermissionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder; 
use Movent\PermissionBundle\DependencyInjection\Compiler\MoventPermissionCompilerPass;

class MoventPermissionBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MoventPermissionCompilerPass());
    }
}
