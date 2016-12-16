<?php

namespace Movent\PermissionBundle\Security;

use Movent\PermissionBundle\Security\MoventPermissionInterface;

/**
 * Mask
 */
class TreeBuilder
{     
	const ID_PREFIX		= 'mvpt_';
	
	protected $id		= null;
	protected $modules	= null;
	protected $template	= null;
	
	protected $config   = array();
	protected $data		= array();
	
	public function __construct($config)
	{
		$this->id 	  = self::ID_PREFIX . sha1(date("Ymdhis"));
		$this->config = $config;
	} 
	
	public function getId(){
		return $this->id;
	}
	
	protected function buildTree()
	{  
		$template = 'var tree = $("#'. $this->id .'").jstree({"plugins": '. 
					 $this->encode($this->config['plugins']) .',"core":';		
					 
		$template .= $this->getTreeConfig();		
		$template .= '});';		
		
		$this->template = $template;
	} 
	
	protected function getTreeConfig()
	{   			
		$config = array('data' 					 => $this->getModulesConfig(),
						"themes" 				 => $this->config['theme'],
				        "expand_selected_onload" => $this->config['expand_selected_onload']); 
					   
		return json_encode($config);		
	}
	
	protected function getModulesConfig()
	{ 			 
		$modules = array();
		foreach($this->modules as $module){ 
			array_push($modules,array("id" 				=> $module->getCode(),
									  "text"			=> $module->getTitle(),
									  "state"			=> array("selected" => false),
									  "children"		=> $this->getModulesActionConfig($module->getCode())));
		}		
		return $modules;  
	}

	public function getModulesActionConfig($module_code)
	{  
		$module = $this->modules[$module_code];
		$config	= array();
		 		
		if($actions = $module->getPermittedMaskedActions()){
			foreach($actions as $action => $mask){ 
				if(is_array($mask)){ 
					$_child_config = array();
					$group 		   = '';
					foreach($mask as $details){
						$group = $details['group'];
						array_push($_child_config,array("id"    => $module_code . "_" . $details['action'],
													    "text"  => $this->humanize($details['title']),
														"state" => array("selected" => $this->isSelected($module_code,$details))));
					}
					array_push($config,array("id" 	    => $module_code . "_" . $group,
											 "text"     => $this->humanize($action),
											 "children" => $_child_config));
				}else{
					array_push($config,array("id"    => $module_code . "_" . $action,
											 "text"  => $this->humanize($action),
											 "state" => array("selected" => $this->isSelected($module_code,$mask))));
				}
			}
		}
		
		return $config;
		
	}
	
	protected function humanize($action)
	{
		$text = strtolower(preg_replace('/[_\s]+/', ' ', $action));
		return ucwords($text); 
	}
	
	protected function isSelected($code,$mask)
	{
		if(is_array($mask)){ //details  
			if(isset($this->data[$code]) && isset($this->data[$code]->$mask['group']) && ($mask['mask'] & $this->data[$code]->$mask['group'])){
				return true;
			} 			
		}else{		
			if(isset($this->data[$code]) && ($mask & $this->data[$code])){
				return true;
			}		
		}
		return false;
	}
	 
	public function addModule($title, MoventPermissionInterface $module,$actions)
	{    
		$module->setTitle($title);
		$module->mergeActions($actions)
			   ->validate();
		
		$this->modules[$module->getCode()] = $module; 
		
		return $this; 
	}   
	 
	
	public function getModules()
	{
		return $this->modules;
	}
	
	public function render()
	{
		$this->buildTree();		
		return $this->template;
	}
	
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	/* 
	 *  Cleans up data from comma separated value to  
	 *  array, then convert to matched Mask ID 
	 *  @returns $this
	 *
	 */
	public function clean()
	{ 
		$new_data 		= array();
		$data 	  		= explode(",",$this->data); 
		$module_codes	= array(); 
		
		foreach($data as $info){ 
			if(strpos($info,"_") !== false){
				$params				= explode("_",$info,2);	 
				$module_code   		= $params[0];
				$module_codes[]	    = $module_code;
			}
		}  
		
		if($module_codes){   
			$module_codes = array_unique($module_codes);
			foreach($module_codes as $module_code ){ 
				if(!isset($new_data[$module_code]) || !is_array($new_data[$module_code])){
					$new_data[$module_code] = array();
				} 
				
				if($actions    = $this->modules[$module_code]->getPermittedMaskedActions()){ 
				
					$configs 	 = array(); 	
					$to_convert  = array();
					foreach($actions as $action => $mask){  
						if(is_array($mask)){  
							$group_configs = array();
							foreach($mask as $details){
								 if(in_array($module_code . "_" . $details['action'],$data)){   
									array_push($group_configs,$details['action']); 
								}
							 }  
							 if($group_configs){ 
								 $configs[$action] = $this->modules[$module_code]->getMask($group_configs);
							 } 
							
						}else{   
							if(in_array($module_code . "_" . $action,$data)){
								array_push($to_convert,$action);
							}
						}
					}
					if($to_convert){
						$new_data[$module_code] =  $this->modules[$module_code]->getMask($to_convert);
					}else{
						$new_data[$module_code] = $configs;
					}
				} 
			}   
		}	     
		$this->data = $new_data;		 
		 
		return $this;
	}
	
	/* 
	 *  Convert Array of Human-readable permission
	 *  to corrensponding Mask ID
	 *  Deprecated ****
	 */
	 
	protected function codeToMask()
	{ 
		$data = array();
		foreach($this->data as $code=>$actions){ 
			if(is_array($actions)){
				foreach($actions as $key=>$action){ 
					$data[$code][$key] = $this->modules[$code]->getMask($actions); 
				} 
			}else{
				$data[$code] = $this->modules[$code]->getMask($actions); 
			}
		}		
		$this->data = $data;
	}
	
	public function encode($data = false)
	{
		return $data === false ? json_encode($this->data) : json_encode($data);
	}
	
	public function normalize($data,$action_only = false)
	{     
		$data 			= (array) json_decode($data);
		$this->data		= $data;
		$formatted_data = array(); 
	}
	 
	public function getModulesWithActions($module_action)
	{      
		$list = array();  
		
		if($this->modules == null){
			return $list;
		}
		
		foreach($this->modules as $module){
			if($actions    = $module->getPermittedMaskedActions()){
				foreach($actions as $action => $detail){
					if(is_array($detail)){ 
						array_push($list,$module->getCode() . '_' . $action);
						foreach($detail as $info){  
							array_push($list,$module->getCode() . '_' . $info['action']); 
						}
					}else{    
						array_push($list,$module->getCode() . '_' . $action); 
					} 
				}
			}
		}  
		return $list;
	}
	
	public function normalizeByModule($module_code,$raw_permissions)
	{
		$data 			= (array) json_decode($raw_permissions); 
		$formatted_data = array(); 
		if(isset($data[$module_code])){
			foreach($this->modules[$module_code]->getPermittedMaskedActions() as $action=>$mask){		
				if($mask & $data[$module_code]){
					$formatted_data[] = $action;
				}
			} 
		}
		return $formatted_data;
	}
	
	protected function decorate($code,$action)
	{
		return $code . '_' . $action;
	}
	
	public function getModuleCodes()
	{ 
		return array_keys($this->modules);
	}
	
	public function isAllowed($requested_permission,$permissions)
	{
		$permissions = (array) json_decode($permissions,true);  
		
		if(strpos($requested_permission,"_") === false){  
			if(isset($permissions[$requested_permission])){
				return true;
			}
			return false;
		}else{
			$params  		 = explode("_",$requested_permission,2);
			$module_code 	 = $params[0]; 
			$module_action 	 = $params[1]; 
		}  
		  
		$permission_actions = $this->getModulePermissionActions($this->modules[$module_code]);
		$mask 				= null; 
		$group 				= null; 
		$stop 				= false;  
		
		  
		foreach($permission_actions as $action_code => $action_mask){
			if(is_array($action_mask)){  
			
				if($action_code == $module_action && isset($permissions[$module_code][$action_code])){   
					return true;
				}
				
				foreach($action_mask as $details){
					if($details['action'] == $module_action){ 
						$mask  = $details['mask']; 
						$group = $details['group']; 
						$stop  = true;
						break;
					}
				}
			}else{
				if($action_code == $module_action){
					$mask = $action_mask; 
					break;
				} 
			}
			
			if($stop){
				break;
			}
		}
		 
		if($this->isPermitted($permissions,$mask,$group,$module_code)){
			return true;
		}  
		
		return false;
	}
	
	protected function isPermitted($permissions,$mask,$group,$module_code)
	{
		if(!$permissions){
			return false;
		}  
						
		foreach($permissions as $permission_module_code => $permission){
			if(is_array($permission)){
				foreach($permission as $action_group => $permission_mask){
					if($permission_module_code == $module_code && $action_group ==  $group && ($permission_mask & $mask)){  
						return true;					
					}
				}
			}else{
				if($permission_module_code == $module_code && ($permission & $mask)){  
					return true; 
				}
			}	
		}		
		return false;
	}
	
	
	protected function getModulePermissionActions($module)
	{
		$actions    = $module->getPermittedMaskedActions();
		return $actions;
	}
}