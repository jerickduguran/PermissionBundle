<?php

namespace Movent\PermissionBundle\Controller\Admin; 

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;  

use Sonata\AdminBundle\Controller\CRUDController; 

class PermissionController extends CRUDController
{ 
    public function settingsAction()
    {   
        return $this->render('MoventPermissionBundle:Admin/Permission:settings.html.twig',
							array('admin_pool' => $this->get('sonata.admin.pool'), 
								  'admin' 	   => $this->admin,
								  'action'	   => 'settings'));
    }
	 
}