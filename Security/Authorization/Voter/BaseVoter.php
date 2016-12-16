<?php
namespace Movent\PermissionBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
 
use Movent\PermissionBundle\Security\TreeBuilder;

class BaseVoter implements VoterInterface
{   
	private $permissionTree; 
	
	public function __construct(TreeBuilder $tree)
	{
		$this->permissionTree = $tree;
	}
	
    public function supportsAttribute($attribute)
    {	  
		if(strpos($attribute,"_") === false){ 
			return in_array($attribute,$this->permissionTree->getModuleCodes());
		}	 
		return in_array($attribute,$this->permissionTree->getModulesWithActions($attribute)); 
    }

    public function supportsClass($class)
    {  
		return true; 
    }
	
	/* protected function supportsAction($code,$action)
	{
		$this->tree->normalizeByModule($attribute, $group->getPermission());
	} */

    /**
     * @var  $obj
     */
    public function vote(TokenInterface $token, $obj, array $attributes)
    {  
        $user	= $token->getUser();	
		
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }
		
		if ($user->isSuperAdmin()) {
			return VoterInterface::ACCESS_GRANTED;
		}	

	    $attribute = $attributes[0]; 
		
		if(is_object($obj)){ //Object-Level Permission not yet suported, skip
            return VoterInterface::ACCESS_ABSTAIN;
		}  
		
        if (!$this->supportsAttribute($attribute)){ //Skip if code is not on permissible modules
            return VoterInterface::ACCESS_ABSTAIN;
        } 	 
		
		$groups 		= $user->getGroups(); 
		$permissions 	= array();
		 
		foreach($groups as $group){
			$permissions[$attribute] = $this->permissionTree->isAllowed($attribute, $group->getPermission());
		}   
		
		if(isset($permissions[$attribute]) && $permissions[$attribute] === true ){ 
			return VoterInterface::ACCESS_GRANTED;
		}
		 
        return VoterInterface::ACCESS_DENIED;
    } 
}
 