<?php

namespace Movent\PermissionBundle\Security;
 
interface MoventPermissionInterface
{     
	 public function getPermittedActions();
	 public function isValid();
}