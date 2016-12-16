<?php

namespace Movent\PermissionBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface; 

use Sonata\AdminBundle\Route\RouteCollection;

class PermissionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('role')
            ->add('permissions')
            ->add('id')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('role')
            ->add('permissions')
            ->add('id')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('role')
            ->add('permissions')
            ->add('id')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('role')
            ->add('permissions')
            ->add('id')
        ;
    }
	
	protected function configureRoutes(RouteCollection $collection)
    {  
		$collection->add('settings', 'role-permission',array(
                '_controller' => sprintf('%s:%s', $this->getBaseControllerName(), 'settings')
				)); 
	} 
	
	public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {  
        if (!$menu) {
            $menu = $this->menuFactory->createItem('root'); 
            $menu = $menu->addChild(
                $this->trans($this->getLabelTranslatorStrategy()->getLabel('dashboard', 'breadcrumb', 'link'), array(), 'SonataAdminBundle'),
                array('uri' => $this->routeGenerator->generate('sonata_admin_dashboard'))
            );
        }
		
		$menu = $menu->addChild(
					'Role Permission',
					array('uri' => null )
				);
		 
        return $this->breadcrumbs[$action] = $menu;
    } 
	
	public function roleOptions()
	{
		$options = array();
		
		$options['ROLE_PLANNER']   		 =  'Planner';
		$options['ROLE_DEVELOPER'] 		 = 'Developer';
		$options['ROLE_CAMAPGINMANAGER'] = 'Campaign Manager';
		$options['ROLE_BRANDMANAGER'] 	 = 'Brand Manager';
		$options['ROLE_ADMINUSER'] 	     = 'Admin';
		
		$_options 						 = "<option value=''></option>";
		
		foreach($options as $code=>$title){
			$_options .= "<option value='".$code."'>".$title."</option>";
		}
		
		return $_options;
	}
}
