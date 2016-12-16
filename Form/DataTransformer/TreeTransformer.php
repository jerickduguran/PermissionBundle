<?php
namespace Movent\PermissionBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException; 
use Movent\PermissionBundle\Security\TreeBuilder;

class TreeTransformer implements DataTransformerInterface
{  
    private $tree;  
	
    public function __construct(TreeBuilder &$tree)
    {
        $this->tree   = $tree; 
    }
 
    public function transform($values)
    {
        if (null === $values){
            return "";
        }    		
		
		$this->tree->normalize($values);	 
    }
 
    public function reverseTransform($config)
    { 	  		 
        if (!$config){
            return null;
        } 
		
        $data = $this->tree->setData($config)->clean()->encode();	 	
		
		return $data;
    }
}