<?php

namespace Movent\PermissionBundle\Security\Core;

use Application\Sonata\UserBundle\Entity\User;
use Movent\PermissionBundle\Security\TreeBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityContext
{
	private $permissionTree; 
	
	public function __construct(TreeBuilder $tree)
	{
		$this->permissionTree = $tree;
	} 
	
	public function isGranted($access_code, UserInterface $user)
    {    
        $groups 		= $user->getGroups(); 
		$permissions 	= array();
		
		if($groups)
		{
			foreach($groups as $group){
				$permissions[$access_code] = $this->permissionTree->isAllowed($access_code, $group->getPermission());
			}   
			
			if(isset($permissions[$access_code]) && $permissions[$access_code] === true ){ 
				return true;
			} 
		}
		
        return false;
    } 
}