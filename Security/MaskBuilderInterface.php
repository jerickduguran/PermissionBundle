<?php
namespace Movent\PermissionBundle\Security;
 
interface MaskBuilderInterface
{  
	  public function add($mask);
	  public function get();
	  public function reset(); 
}