<?php
namespace Movent\PermissionBundle\Entity;

use Sonata\CoreBundle\Model\BaseManager; 
use Movent\PermissionBundle\Entity\RoleManagerInterface;


class RoleManager extends BaseManager implements RoleManagerInterface
{    
    public function getConnection()
    {
        return $this->getObjectManager()->getConnection();
    }
	
	public function findById($id)
	{
		return $this->getRepository()->findOneById($id);
	}  
	
	public function getRoles()
	{
		return $this->getRepository()->findAll();
	}
}