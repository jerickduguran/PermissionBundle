<?php

namespace Movent\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('movent_permission');
 
		$this->addModuleSection($rootNode);	
		$this->addJstreeSection($rootNode);	
		
        return $treeBuilder;
    }
	
	protected function addModuleSection(ArrayNodeDefinition $node){
		$node->children() 
					->arrayNode('modules') 
						->prototype('array')      
							->prototype('variable')->end() 
						->end()  
					->end()
				->end();  
	}
	
	protected function addJstreeSection(ArrayNodeDefinition $node)
	{
		$node->children() 
					->arrayNode('jstree') 
						->children()      
							->arrayNode('theme') 
								->prototype('scalar')->end()  
							->end()
							->arrayNode('plugins')
								->prototype('scalar')->end()  
							->end() 
							->booleanNode('expand_selected_onload')->defaultTrue()->end() 
						->end()  
					->end()
				->end(); 
	}
}
